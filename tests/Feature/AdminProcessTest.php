<?php

namespace Tests\Feature;

use App\Models\ReviewRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** TC18–TC23: administrator dashboard and processing. */
class AdminProcessTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->user('admin'));
    }

    private function pendingRequest(): ReviewRequest
    {
        return ReviewRequest::where('subject_code', 'KPF3012')->firstOrFail();
    }

    public function test_tc18_admin_dashboard_shows_totals_and_pending(): void
    {
        $this->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Welcome, Admin')
            ->assertViewHas('stats', ['total' => 10, 'pending' => 3, 'approved' => 6, 'rejected' => 1])
            ->assertViewHas('requests', fn ($r) => $r->count() === 3);
    }

    public function test_tc19_all_requests_lists_every_request(): void
    {
        $this->get('/admin/requests')->assertOk()->assertViewHas('requests', fn ($r) => $r->count() === 10);
        $this->get('/admin/requests/pending')->assertOk()->assertViewHas('requests', fn ($r) => $r->count() === 3);
    }

    public function test_tc20_decision_is_required(): void
    {
        $this->post("/admin/requests/{$this->pendingRequest()->id}/process", [])
            ->assertSessionHasErrors(['decision' => 'Please choose Approve or Reject.']);
    }

    public function test_tc21_reject_requires_comment(): void
    {
        $this->post("/admin/requests/{$this->pendingRequest()->id}/process", ['decision' => 'rejected', 'comment' => ''])
            ->assertSessionHasErrors(['comment' => 'Please give a reason when rejecting a request.']);
        $this->assertTrue($this->pendingRequest()->isPending());
    }

    public function test_tc22_approve_with_comment(): void
    {
        $r = $this->pendingRequest();

        $this->get("/admin/requests/{$r->id}/process")->assertOk()->assertSee('Process Academic Review Request');
        $this->post("/admin/requests/{$r->id}/process", ['decision' => 'approved', 'comment' => 'Re-marked. Grade updated to A-.'])
            ->assertRedirect('/admin/requests/pending')
            ->assertSessionHas('success');

        $r->refresh();
        $this->assertSame('approved', $r->status);
        $this->assertSame('Re-marked. Grade updated to A-.', $r->admin_comment);
        $this->assertSame($this->user('admin')->id, $r->decided_by);
        $this->assertNotNull($r->decided_at);
        $this->get('/admin/requests/pending')->assertViewHas('requests', fn ($x) => $x->count() === 2);
    }

    public function test_tc23_processed_request_cannot_be_processed_again(): void
    {
        $r = ReviewRequest::where('status', 'approved')->firstOrFail();

        $this->get("/admin/requests/{$r->id}/process")
            ->assertRedirect('/admin/requests/pending')
            ->assertSessionHas('error', 'This request has already been approved.');

        $this->post("/admin/requests/{$r->id}/process", ['decision' => 'rejected', 'comment' => 'Changing my mind'])
            ->assertSessionHas('error');
        $this->assertSame('approved', $r->fresh()->status);
    }

    public function test_unknown_request_returns_404(): void
    {
        $this->get('/admin/requests/999/process')->assertNotFound();
    }

    public function test_student_sees_decision_after_processing(): void
    {
        $r = $this->pendingRequest();
        $this->post("/admin/requests/{$r->id}/process", ['decision' => 'approved', 'comment' => 'Grade updated.']);

        $this->actingAs($this->user('syamimi'))
            ->get('/student/dashboard')
            ->assertSee('Grade updated.')
            ->assertViewHas('stats', fn ($s) => $s['approved'] === 3 && $s['pending'] === 0);
    }
}
