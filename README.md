# Student Academic Review System (SARS) — v1.0 (Laravel)

First working release (Iteration 1) built from the DES3083 Group 3 wireframes.
Students submit academic review requests; the administrator approves or rejects them.

**Stack:** Laravel 13 (PHP 8.3+) · MySQL / MariaDB · Blade views · plain CSS and JavaScript (no npm build needed).

---

## 1. Requirements

- PHP **8.3 or newer** (Laragon 6+ or XAMPP 8.3+)
- Composer
- MySQL / MariaDB
- PHP extensions: pdo_mysql, mbstring, openssl, fileinfo, intl (all enabled by default in Laragon)

Check with: `php -v` and `composer -V`

---

## 2. Setup (Laragon or XAMPP)

1. Extract the folder, e.g. `C:\laragon\www\sars` (or `C:\xampp\htdocs\sars`).
2. Create an empty database called **`sars_db`** in phpMyAdmin / HeidiSQL.
3. Open a terminal in the project folder and run:

```
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

4. Open <http://127.0.0.1:8000>

If your MySQL user has a password, edit `DB_USERNAME` / `DB_PASSWORD` in `.env` before running `migrate`.

**Laragon shortcut:** after step 3 you can skip `php artisan serve` and open `http://sars.test`.

### Sample logins

| Role    | Username | Password |
|---------|----------|----------|
| Admin   | `admin`   | `Admin@123` |
| Student | `syamimi` | `Student@123` |
| Student | `aminah`, `amani`, `nurul` | `Student@123` |

Sample data matches the wireframe: admin sees 10 total / 3 pending / 6 approved / 1 rejected; Syamimi sees 3 / 1 / 2 / 0.

### Reset the database
```
php artisan migrate:fresh --seed
```

### Run the automated tests
```
php artisan test
```
Tests use an in-memory SQLite database, so they never touch your `sars_db` data.

---

## 3. Configuration (`.env`)

| Key | Default | Notes |
|---|---|---|
| `APP_NAME` | Student Academic Review System | |
| `APP_DEBUG` | `true` | set `false` before deploying |
| `APP_TIMEZONE` | `Asia/Kuala_Lumpur` | dates shown as dd/mm/yyyy |
| `DB_CONNECTION` | `mysql` | |
| `DB_DATABASE` | `sars_db` | |
| `DB_USERNAME` / `DB_PASSWORD` | `root` / *(empty)* | Laragon/XAMPP default |
| `SESSION_DRIVER` | `database` | sessions table is created by the migration |

No external services or API keys are needed.

---

## 4. Project structure (main files)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php                 Login, logout, login lockout
│   │   ├── Student/DashboardController.php    Student dashboard
│   │   ├── Student/ReviewRequestController.php  List, add, update, delete own requests
│   │   ├── Admin/DashboardController.php      Admin dashboard
│   │   └── Admin/ProcessRequestController.php Pending, all requests, approve/reject
│   ├── Middleware/EnsureRole.php              role:student / role:admin
│   └── Requests/ReviewRequestForm.php         Add/Update validation rules
├── Models/User.php                            username, name, role
└── Models/ReviewRequest.php                   status rules, dashboard stats
database/
├── migrations/                                users, sessions, cache, jobs, review_requests
└── seeders/DatabaseSeeder.php                 sample users and requests
resources/views/
├── layouts/app.blade.php                      top bar + sidebar
├── partials/                                  stat cards, tables, modals
├── auth/login.blade.php
├── student/ dashboard, requests, form
└── admin/   dashboard, pending, requests, process
public/css/app.css, public/js/app.js           design system + UI behaviour
routes/web.php                                 all routes
tests/Feature/                                 AuthTest, StudentRequestTest, AdminProcessTest
```

### Routes

| Method | URL | Purpose | Access |
|---|---|---|---|
| GET/POST | `/`, `/login` | Login | Guest |
| POST | `/logout` | Logout | Logged in |
| GET | `/student/dashboard` | Student dashboard | Student |
| GET | `/student/requests` | My requests | Student |
| GET/POST | `/student/requests/create`, `/student/requests` | Add request | Student |
| GET/PUT | `/student/requests/{id}/edit`, `/student/requests/{id}` | Update request | Student (own, pending) |
| DELETE | `/student/requests/{id}` | Delete request | Student (own, pending) |
| GET | `/admin/dashboard` | Admin dashboard | Admin |
| GET | `/admin/requests/pending` | Pending requests | Admin |
| GET | `/admin/requests` | All requests | Admin |
| GET/POST | `/admin/requests/{id}/process` | Approve / reject | Admin (pending only) |

### Data model
- **users** — id, username (unique), name, password (hashed), role (`student`/`admin`), timestamps
- **review_requests** — id, student_id → users, subject_code, justification, status (`pending`/`approved`/`rejected`), admin_comment, decided_by → users, decided_at, timestamps

---

## 5. MVP scope

**Must have (done)** — login/logout with roles; student dashboard, add, view, update, delete (pending only); admin dashboard, pending list, all requests, approve/reject with comment; validation and messages; responsive layout.

**Should have (done)** — duplicate pending check per subject; comment required when rejecting; login lockout (5 attempts / 5 minutes); delete confirmation.

**Future release** — registration and password reset; email notifications; search, filter and pagination; file attachments; admin user management; activity log and export.

## 6. Assumptions
1. Subject code = 3 letters + 4 digits (e.g. `DES3083`), auto-uppercased.
2. Justification 20–1000 characters.
3. "Forgot password?" shows a contact-admin message.
4. Accounts are created by the admin/seeder (no sign-up screen in the wireframe).
5. Comment is required when rejecting, optional when approving.
6. View button added to the student dashboard; Status column added to All Requests.

## 7. Security
- Passwords hashed with bcrypt (Laravel `hashed` cast)
- Eloquent / query builder only (parameterised queries)
- Blade `{{ }}` escaping on all output
- CSRF token on every form (`@csrf`); expired token → back to login with a message
- Session regenerated on login, invalidated on logout
- `role` middleware + ownership and status checks on the server
- Login rate limit: 5 failed attempts per username + IP → 5-minute lock

## 8. Test results (v1.0)
- **PHPUnit:** 28 tests, 101 assertions, all passed (`php artisan test`).
- **Browser check (Chromium):** 17 checks passed — add, delete via modal, view modal, logout, approve, mobile menu, no horizontal scroll at 1366 / 820 / 390 px, no JavaScript errors.
- Tested here with SQLite; **MySQL was not run in this environment** — run `php artisan migrate --seed` and the manual checks below once on Laragon/XAMPP.

### Manual test checklist
| # | Test | Expected |
|---|---|---|
| 1 | Open `/student/dashboard` without login | Redirect to login, "Please log in" |
| 2 | Wrong password | "Incorrect username or password" |
| 3 | 5 wrong passwords | Locked for 5 minutes |
| 4 | Login `syamimi` | Student dashboard 3/1/2/0 |
| 5 | Student opens `/admin/dashboard` | Blocked |
| 6 | Add request with empty / invalid fields | Field errors |
| 7 | Add `KPF3012` again | Duplicate error |
| 8 | Add valid request | Success, Pending |
| 9 | Edit pending request | Saved |
| 10 | Delete → Cancel / Delete | Kept / removed |
| 11 | View button | Pop-up with details |
| 12 | Logout → Cancel / Logout | Stay / back to login |
| 13 | Login `admin` | 10/3/6/1 |
| 14 | Process with no decision / reject without comment | Errors |
| 15 | Approve | Removed from pending |
| 16 | Phone size in DevTools | Hamburger menu, card tables |

## 9. Known limitations
- No password reset, registration or user management screens.
- No email notifications; no search/pagination.
- Not production-ready: needs HTTPS, `APP_DEBUG=false`, a non-root DB user and backups.

## 10. Roadmap (Iteration 2)
1. Search, status filter and pagination
2. Password reset / change password
3. Email notification on decision
4. Supporting document upload
5. Admin user management
6. Activity log and PDF/Excel export

## 11. Git tag v1.0
```
git init
git add .
git commit -m "Iteration 1: SARS v1.0 initial implementation (Laravel)"
git tag -a v1.0 -m "Iteration 1 release"
git remote add origin https://github.com/<username>/sars.git
git branch -M main
git push -u origin main --tags
```
`vendor/` and `.env` are already excluded by `.gitignore`.
