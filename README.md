# Ticket Approval System

## Overview
This project implements a **Ticket Submission and Multi‑Level Approval System** using **Laravel (backend)** and **Vue.js (frontend inside Blade)**.  

Users can submit tickets, and the tickets go through a **two‑stage admin approval workflow**:

1. **Admin 1** reviews the submitted ticket.
2. **Admin 2** reviews tickets after Admin 1’s decision.

Admins can **approve or reject tickets and change their decisions if necessary**. The system keeps the ticket state visible while allowing corrections.

## Assumptions
1. Admins may change their mind and rethink their decision.
2. No over engineering and forcefully using every pattern.
3. No over-documenting. Only documenting where code needs expalining.
4. Front-end design in not the main point of this challenge.

---

# Installation

## Using docker:

`git clone https://github.com/danialtajalli/RahWebHirirngTask`   
`cd [project folder]`   
Run `docker compose up --build`   

## Traditional way

## 1. Clone the repository

```bash
git clone https://github.com/danialtajalli/RahWebHirirngTask
cd [project folder]
```

---

## 2. Install dependencies

```bash
composer install
npm install
```

---

## 3. Configure environment

Copy `.env.example`:

```bash
cp .env.example .env
```

Set your database configuration inside `.env`.

Example:

```
DB_DATABASE=ticket_system
DB_USERNAME=root
DB_PASSWORD=
```

Add Sanctum configuration inside `.env`.

Example:
```
SESSION_DRIVER=cookie
SESSION_DOMAIN=127.0.0.1
SANCTUM_STATEFUL_DOMAINS=127.0.0.1:8000
```

---

## 4. Generate application key

```bash
php artisan key:generate
```

---

## 5. Run migrations

```bash
php artisan migrate
```

---

## 6. Start the application

```bash
php artisan serve
```

Open in browser:

```
http://127.0.0.1:8000
```
---

# Structure and Architecture:

Using enums to manage tickets state and controlling the enums flow through enum functions.
Using Laravel's built-in request functionality to manage auth and ticket submission.
Also, using Laravel's built in policies to make sure states are managed properly.
Using a service to manage state transtiotions, aiming for seperation of concerns using jobs 
and events and service. Implementing the Adapter pattern for calling external API and using it in the service.
Also, aiming for not over engineering and using too many patterns in the system.
Using database driver for queues to keep things simple.
Implementing notifications using Laravel's built-in notifications.

# Features

## User Features
- User authentication (login/logout)
- Submit new tickets
- View submitted tickets
- Track ticket status

## Admin Features
- View tickets assigned to their approval level
- Approve or reject tickets
- Change approval decisions if a mistake was made
- Real‑time UI updates after actions

---

# Ticket Workflow

```
User submits ticket
        ↓
State: submitted
        ↓
Admin 1 review
   ├── reject  → rejected_by_admin1
   └── approve → approved_by_admin1
        ↓
Admin 2 review
   ├── reject  → rejected_by_admin2
   └── approve → approved_by_admin2
        ↓
Send to external API
    ├── Fail  → Try again in 1h for three times
    └── approve → Success
```

Admins can **change their previous decision**, and the UI keeps the action buttons visible.

---

# Tech Stack

Backend:
- Laravel
- Laravel Sanctum (authentication)
- MySQL

Frontend:
- Vue.js
- Axios
- Blade templates

---


---

# Authentication

The system uses **Laravel Sanctum** with session-based authentication.

Logout properly invalidates the session:

```php
Auth::guard('web')->logout();
$request->session()->invalidate();
$request->session()->regenerateToken();
```

---

# API Endpoints

## User

```
POST /register
POST /login
POST /login_admin
POST /logout
POST /api/tickets
GET  /api/tickets
GET  /api/tickets/{ticket}
GET  /api/admin/tickets/admin1
GET  /api/admin/tickets/admin2
POST  /api/admin/tickets/fake-external-service
```

---

## Admin 1

```
GET  /api/admin/tickets/admin1
POST /api/admin/tickets/{id}/approve-admin-1
POST /api/admin/tickets/{id}/reject-admin-1
POST  /api/admin/tickets/bulk-approve-admin-1
POST  /api/admin/tickets/bulk-reject-admin-1
```

---

## Admin 2

```
GET  /api/admin/tickets/admin2
POST /api/admin/tickets/{id}/approve-admin-2
POST /api/admin/tickets/{id}/reject-admin-2
POST  /api/admin/tickets/bulk-approve-admin-2
POST  /api/admin/tickets/bulk-reject-admin-2

```

---

# Ticket States

| State | Description |
|------|-------------|
| submitted | Ticket created by user |
| approved_by_admin1 | Approved by Admin 1 |
| rejected_by_admin1 | Rejected by Admin 1 |
| rejected_by_admin2 | Rejected by Admin 2 |
| external_processing | Approved by Admin 2 and sent to queue to contact API|
| external_failed | Failed in contacting API part for whatever reason |
| success | Contacted API and returned with success code |

---

# Example Test Accounts

Example accounts for testing:

```
Admin 1
email: admin1@test.com
password: password

Admin 2
email: admin2@test.com
password: password
```

---

# Security

- All admin routes are protected using **auth:sanctum middleware**
- Backend validates ticket state transitions
- Frontend only handles UI permissions

---

# Improvements (Future Work)

Possible enhancements:

- Email alerts
- Pagination for tickets
- Activity logs
- Role-based middleware
- UI improvements using a component library

---
