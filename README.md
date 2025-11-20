🛒 E-Commerce API — Senior Laravel Developer Assessment

A secure, role-based E-Commerce REST API built with Laravel, featuring clean architecture, JWT authentication, and high-performance MySQL full-text search.

📇 Contact Information
Detail	Value
Name	[Your Name]
Email	[Your Email]
GitHub Profile	[Your GitHub Link]
🚀 1. Project Overview & Features
✅ Completed Core Features

Authentication (JWT) using Sanctum/Passport or tymon/jwt-auth.

Authorization (RBAC): Admin, Vendor, Customer.

Vendor Scoping: Vendors can manage only their own products.

Product Management (CRUD with Variants).

High-Performance Search via MySQL FULLTEXT Boolean Mode.

Clean Architecture using Repository Pattern + Service Layer.

🛠 2. Local Setup Instructions
1. Clone the Repository
git clone [YOUR_REPO_URL]
cd [repo-name]

2. Install PHP Dependencies
composer install

3. Copy Environment File
cp .env.example .env

4. Generate App Key
php artisan key:generate

5. Configure Database

Update .env with MySQL credentials.

6. Run Migrations & Seeders
php artisan migrate --seed

7. Generate JWT Secret (if using tymon/jwt-auth)
php artisan jwt:secret
php artisan config:clear

8. Start Server
php artisan serve

⚙️ 3. Environment Variables
Variable	Description	Example
APP_URL	Base API URL	http://localhost:8000
DB_CONNECTION	Must be MySQL for FTS	mysql
DB_DATABASE	Database name	ecommerce_api
DB_USERNAME	DB user	root
DB_PASSWORD	DB password	null
JWT_SECRET	Secret used for signing JWT tokens	generated via jwt:secret
🔑 4. API Authentication Guide
Login to obtain a token

Endpoint:
POST /api/v1/auth/login

Admin test credentials:

email: asafwan72@gmail.com  
password: 12345678

Use the token in requests
Authorization: Bearer <token_here>


For all roles, permissions, and vendor scoping:
➡️ See API_Authentication_Guide.md

🧪 5. Testing Instructions

Run all tests:

php artisan test


Unit tests for Services & Repositories included.
Feature tests partially incomplete.

📁 Project Architecture (High-Level)
app/
  Http/Controllers/
  Services/
  Repositories/
  Models/
database/migrations/
routes/api.php
