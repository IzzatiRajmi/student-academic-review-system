<?php

namespace Tests\Feature;

use App\Models\ReviewRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** TC08–TC16, TC24: student add / update / delete rules. */
class StudentRequestTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user('syamimi'));
    }

    private function otherStudentsRequest(): ReviewRequest
    {
        return ReviewRequest::where('student_id', $this->user('aminah')->id)->firstOrFail();
    }

    private function approvedOwnRequest(): ReviewRequest
    {
        return ReviewRequest::where('student_id', $this->user('syamimi')->id)->where('status', 'approved')->firstOrFail();
    }

    public function test_tc08_invalid_input_is_rejected_and_kept(): void
    {
        $this->from('/student/requests/create')
            ->post('/student/requests', ['subject_code' => 'ab12', 'justification' => 'short'])
            ->assertRedirect('/student/requests/create')
            ->assertSessionHasErrors([
                'subject_code' => 'Use 3 letters followed by 4 digits, e.g. DES3083.',
                'justification' => 'Please explain a little more (at least 20 characters).',
            ])
            ->assertSessionHasInput('subject_code', 'ab12');
    }

    public function test_tc09_duplicate_pending_subject_is_rejected(): void
    {
        $this->post('/student/requests', ['subject_code' => 'KPF3012', 'justification' => 'Another request for the same subject code please.'])
            ->assertSessionHasErrors(['subject_code' => 'You already have a pending request for this subject.']);
    }

    public function test_tc10_valid_request_is_saved_as_pending(): void
    {
        $this->post('/student/requests', ['subject_code' => 'des 3083', 'justification' => 'Please review my final project marks for this subject.'])
            ->assertRedirect('/student/requests')
            ->assertSessionHas('success', 'Request for DES3083 submitted successfully.');

        $this->assertDatabaseHas('review_requests', [
            'student_id' => $this->user('syamimi')->id, 'subject_code' => 'DES3083', 'status' => 'pending',
        ]);
        $this->get('/student/dashboard')->assertViewHas('stats', fn ($s) => $s['total'] === 4 && $s['pending'] === 2);
    }

    public function test_tc11_own_pending_request_can_be_updated(): void
    {
        $r = ReviewRequest::where('subject_code', 'KPF3012')->firstOrFail();

        $this->get("/student/requests/{$r->id}/edit")->assertOk()->assertSee('Update Academic Review Request');
        $this->put("/student/requests/{$r->id}", ['subject_code' => 'KPF3012', 'justification' => 'Updated justification text with enough length.'])
            ->assertRedirect('/student/requests')
            ->assertSessionHas('success');

        $this->assertSame('Updated justification text with enough length.', $r->fresh()->justification);
    }

    public function test_tc12_approved_request_cannot_be_updated(): void
    {
        $r = $this->approvedOwnRequest();

        $this->get("/student/requests/{$r->id}/edit")
            ->assertRedirect('/student/requests')
            ->assertSessionHas('error', 'This request has already been approved and can no longer be updated.');

        $this->put("/student/requests/{$r->id}", ['subject_code' => 'XYZ1234', 'justification' => 'Trying to change an approved request.'])
            ->assertRedirect('/student/requests');
        $this->assertNotSame('XYZ1234', $r->fresh()->subject_code);
    }

    public function test_tc13_cannot_open_another_students_request(): void
    {
        $this->get("/student/requests/{$this->otherStudentsRequest()->id}/edit")->assertNotFound();
    }

    public function test_tc14_approved_request_cannot_be_deleted(): void
    {
        $r = $this->approvedOwnRequest();

        $this->delete("/student/requests/{$r->id}")
            ->assertSessionHas('error', 'Only pending requests can be deleted.');
        $this->assertModelExists($r);
    }

    public function test_tc15_cannot_delete_another_students_request(): void
    {
        $r = $this->otherStudentsRequest();

        $this->delete("/student/requests/{$r->id}")->assertSessionHas('error', 'Request not found.');
        $this->assertModelExists($r);
    }

    public function test_tc16_own_pending_request_can_be_deleted(): void
    {
        $r = ReviewRequest::where('subject_code', 'KPF3012')->firstOrFail();

        $this->delete("/student/requests/{$r->id}", ['back' => 'dashboard'])
            ->assertRedirect('/student/dashboard')
            ->assertSessionHas('success', 'Request for KPF3012 deleted.');
        $this->assertModelMissing($r);
    }

    public function test_tc24_script_input_is_escaped(): void
    {
        $this->post('/student/requests', ['subject_code' => 'DES3083', 'justification' => '<script>alert(1)</script> please review my final exam grade.']);

        $html = $this->get('/student/requests')->getContent();
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('u003Cscript', $html); // JSON-escaped, shown as text
    }

    public function test_student_only_sees_own_requests(): void
    {
        $this->get('/student/requests')
            ->assertOk()
            ->assertSee('KPF3012')
            ->assertDontSee('KPF3022'); // Aminah's request
    }
}
