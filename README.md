🛒 E-Commerce API — Senior Laravel Developer Assessment

A secure, role-based E-Commerce REST API built with Laravel, featuring a clean architecture, robust security, role-based authorization, and high-performance full-text product search.

📇 Contact Information
Detail	Value
Name	[Your Name]
Email	[Your Email]
GitHub Profile	[Your GitHub Link]
🚀 1. Project Overview & Features

This project delivers the core architecture of an E-Commerce backend API, focusing on security, data integrity, and clean structure.
Transactional workflows (Orders) are intentionally out of scope for this submission.

✅ Completed Core Features
🔐 Authentication (JWT)

Full token-based API authentication.

Implemented using Laravel Sanctum/Passport or tymon/jwt-auth.

🔏 Authorization (RBAC)

Strict Role-Based Access Control:

Admin: Full access.

Vendor: Restricted to their own products only.

Customer: Limited access to browsing.

🛍 Vendor Scoping

Vendors may only view, update, and delete their own products.

Admins bypass all scoping restrictions.

📦 Product Management

Full CRUD operations for:

Products

Variants

🔎 High-Performance Search

Implemented using MySQL FULLTEXT Search in Boolean Mode.

Provides fast, accurate product search results.

🏗 Architectural Standards

Strict adherence to the Repository Pattern.

Service classes handle business logic.

Controllers remain thin and clean.

Easily testable and maintainable codebase.

🛠 2. Local Setup Instructions

Follow these steps to run the project locally:

1. Clone the Repository
git clone [YOUR_REPO_URL]
cd [repo-name]

2. Install PHP Dependencies
composer install

3. Create Environment File
cp .env.example .env

4. Generate Application Key
php artisan key:generate

5. Configure Database

Update .env with your MySQL credentials.

6. Run Migrations & Seeders

Includes FULLTEXT index for product search.

php artisan migrate --seed

7. Generate JWT Secret (if using tymon/jwt-auth)
php artisan jwt:secret
php artisan config:clear

8. Start Local Server
php artisan serve

⚙️ 3. Environment Variables

Ensure the following important variables are set in your .env:

Variable	Description	Example
APP_URL	Base API URL	http://localhost:8000
DB_CONNECTION	Must be MySQL for FTS	mysql
DB_DATABASE	Database name	ecommerce_api
DB_USERNAME	DB user	root
DB_PASSWORD	DB password	null
JWT_SECRET	Used to sign JWT tokens	(generated)
🔑 4. API Authentication Guide

This API uses JWT Bearer Tokens.

🔐 Login to Get Token

Endpoint:
POST /api/v1/auth/login

Test Admin Credentials:

email: asafwan72@gmail.com  
password: 12345678

🔧 Use the token in protected requests:
Authorization: Bearer <your_token_here>

📘 Detailed Authentication

For all roles, access levels, scoping rules, and test credential details:
➡️ See API_Authentication_Guide.md

🧪 5. Testing Instructions

Run all tests:

php artisan test


Note: Unit tests (Service + Repository layers) are included.
Feature/Integration tests for API endpoints are partially incomplete due to time constraints.

📁 Project Structure (High-Level)
app/
  Http/
    Controllers/
  Services/
  Repositories/
  Models/
database/
  migrations/
routes/
  api.php

📌 Notes

Vendor access is strictly enforced via middleware & service-layer checks.

Product search uses native MySQL FTS for optimal performance.

Controller layer is intentionally thin to preserve maintainability.

🏁 Final Remarks

This submission demonstrates architectural design choices and practical implementation of a scalable, secure Laravel-based E-Commerce backend.
