# Ticket Approval System

## Overview
This project implements a **Ticket Submission and Multi‑Level Approval System** using **Laravel (backend)** and **Vue.js (frontend inside Blade)**.  

Users can submit tickets, and the tickets go through a **two‑stage admin approval workflow**:

1. **Admin 1** reviews the submitted ticket.
2. **Admin 2** reviews tickets after Admin 1’s decision.

Admins can **approve or reject tickets and change their decisions if necessary**. The system keeps the ticket state visible while allowing corrections.

---

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
   ├── approve → approved_by_admin1
   └── reject  → rejected_by_admin1
        ↓
Admin 2 review
   ├── approve → approved_by_admin2
   └── reject  → rejected_by_admin2
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

# Installation

## 1. Clone the repository

```bash
git clone <repository-url>
cd ticket-system
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
POST /login
POST /logout
POST /api/tickets
GET  /api/tickets
```

---

## Admin 1

```
GET  /api/admin/tickets/admin1
POST /api/admin/tickets/{id}/approve-admin-1
POST /api/admin/tickets/{id}/reject-admin-1
```

---

## Admin 2

```
GET  /api/admin/tickets/admin2
POST /api/admin/tickets/{id}/approve-admin-2
POST /api/admin/tickets/{id}/reject-admin-2
```

---

# Ticket States

| State | Description |
|------|-------------|
| submitted | Ticket created by user |
| approved_by_admin1 | Approved by Admin 1 |
| rejected_by_admin1 | Rejected by Admin 1 |
| approved_by_admin2 | Approved by Admin 2 |
| rejected_by_admin2 | Rejected by Admin 2 |

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

User
email: user@test.com
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
