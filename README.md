# Senior Laravel Developer Assessment: E-Commerce API

This project implements a secure, role-based E-Commerce API built on the Laravel framework, focusing on clean architecture, robust security, and high-performance search capabilities, as required by the assignment brief.

---

## 6. Contact Information

| Detail | Value |
|--------|--------|
| **Name** | [Your Name] |
| **Email** | [Your Email] |
| **GitHub Profile** | [Your GitHub Link] |

---

## 1. Project Overview and Features

This submission delivers the core foundation for the E-Commerce platform, prioritizing security and data management over the transactional workflow (Orders).

### Completed Core Features

#### Authentication (JWT)
- Full token-based authentication using Laravel Sanctum/Passport (or equivalent JWT package) for secure API access.

#### Authorization (RBAC)
- Strict Role-Based Access Control enforced across all management endpoints for Admin, Vendor, and Customer roles.

#### Vendor Security Scoping
- Vendors can only manage (view, update, delete) the products they own.
- Admins have unrestricted access.

#### Product Management (CRUD)
- Complete functionality for creating, reading, updating, and deleting Products and their associated Variants.

#### High-Performance Search
- Product search implemented using MySQL Full-Text Search (FTS) in Boolean Mode for fast and relevant results.

### Architectural Focus
- Strict adherence to the Repository Pattern.
- Service classes to separate business logic from controller layer.
- Ensures maintainability and testability.

---

## 2. Local Setup Instructions (Step-by-Step)

Follow these steps to get the application running locally:

### 1. Clone the Repository
```bash
git clone [YOUR_REPO_URL]
cd [repo-name]

### 2. Install PHP Dependencies
```bash
composer install

