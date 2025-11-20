API Authentication and Authorization Guide

This guide details the steps required to authenticate and interact with the E-Commerce API endpoints. This API uses JWT (JSON Web Token) for authentication and enforces Role-Based Access Control (RBAC) across all management routes.

1. Authentication (JWT)

All protected endpoints require a valid JWT passed in the request header.

A. Registration (Public)

Detail

Setting

Method

POST

Endpoint

/api/v1/auth/register

Body

raw / JSON (Requires: name, email, password, password_confirmation)

B. Login (Public)

The login endpoint returns the JWT access token and user information upon successful authentication.

Detail

Setting

Method

POST

Endpoint

/api/v1/auth/login

Body

raw / JSON (Requires: email, password)

Success Response Example:

{
    "user": { ... },
    "access_token": "YOUR_JWT_ACCESS_TOKEN_HERE" 
}


C. Using the Access Token

The access_token must be included in the header of every protected request:

Header Key: Authorization

Header Value: Bearer YOUR_JWT_ACCESS_TOKEN_HERE

2. Role-Based Access Control (RBAC)

The API utilizes three distinct roles, defined in the RoleMiddleware, to manage access to endpoints:

Role

Access Level

Examples of Access

Admin

Full Management Access

Can manage all Users, Products, and Orders. No restrictions.

Vendor

Scoped Management Access

Can create, view, update, and delete ONLY their own products and manage ONLY their own orders.

Customer

Read-Only Access

Can only access public (search) or personal (order) endpoints. Forbidden from all Product Management routes.

Specific Authorization Rules for Vendors:

The system implements an explicit ownership check to enforce security:

Allowed: A Vendor (ID: 2) can access /api/v1/products/5 if Product 5 has vendor_id: 2.

Forbidden: A Vendor (ID: 2) will receive a 403 Forbidden if they attempt to access /api/v1/products/6 (a product belonging to Vendor ID: 3).

3. Testing Credentials (From UserSeeder.php)

Use these credentials to test the various role permissions:

Role

Email

Password

Admin

asafwan72@gmail.com

12345678

Vendor

vendor1@example.com

12345678

Customer

customer1@example.com

12345678

Note: This API Documentation should be supplemented with the exported Postman Collection and the generated OpenAPI/Swagger Specification file.
