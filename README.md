# Medical Lead & Appointment Management API

A production-ready RESTful API for managing medical leads and appointments, built with Laravel 12.

##  Features

-   **Authentication**: Secure Token-Based Authentication using Laravel Sanctum.
-   **RBAC**: Role-Based Access Control for Admins and Coordinators.
-   **Lead Management**: Public appointment requests and internal lead tracking.
-   **Auto-Assignment**: Round-Robin algorithm to automatically assign leads to coordinators using Queues.
-   **Notifications**: Email notifications to coordinators upon assignment.
-   **Performance**: Database indexing and pagination implemented.
-   **Architecture**: Service-Repository pattern with Event-Driven design.

## Tech Stack

-   **Framework**: Laravel 12.x
-   **Language**: PHP 8.2+
-   **Database**: MySQL
-   **Queue**: Database Driver
-   **Testing**: PHPUnit / Pest

## Setup Instructions

1.  **Clone the repository**
    ```bash
    git clone <repository-url>
    cd medical-lead-api
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    ```

3.  **Environment Configuration**
    ```bash
    cp .env.example .env
    # Update DB_DATABASE, DB_USERNAME, DB_PASSWORD in .env
    # Set QUEUE_CONNECTION=database
    ```

4.  **Generate Key**
    ```bash
    php artisan key:generate
    ```

5.  **Run Migrations**
    ```bash
    php artisan migrate
    ```

6.  **Serve Application**
    ```bash
    php artisan serve
    ```

7.  **Run Queue Worker** (Critical for Lead Assignment)
    ```bash
    php artisan queue:work
    ```

## Testing

Run the feature tests to verify the application:

```bash
php artisan test
```

##  Architecture Overview

This project follows a **Senior-Level** architecture to ensure scalability and maintainability:

-   **Controllers**: Slim and lightweight. Only handle HTTP requests and responses.
-   **FormRequests**: Handle all validation logic (`StoreLeadRequest`, `UpdateLeadStatusRequest`).
-   **Services**: `LeadService` contains the core business logic.
-   **Events & Listeners**: `LeadCreated` event triggers `AssignLeadListener` to decouple logic.
-   **Jobs**: `AssignLeadJob` handles the background processing of lead assignment.
-   **Resources**: `LeadResource` ensures consistent API response formatting.

## Compliance Checklist

-   [x] **No Business Logic in Controllers**: Moved to Services.
-   [x] **Queues Implemented**: Used for Lead Assignment.
-   [x] **Validation**: Strict validation using FormRequests.
-   [x] **Tests**: Comprehensive Feature Tests included.
-   [x] **Security**: Sanctum & Middleware used.

Postman Collection / API Manual: See [API Instruction Manual](api_instruction_manual.md) for detailed request/response examples

1.login api:->
<img width="1155" height="677" alt="image" src="https://github.com/user-attachments/assets/811af695-4dc3-4010-9ecf-efbf6cdfd1af" />
link:-> post :-> http://127.0.0.1:8000/api/login
api collection:->
{
    "access_token": "4|RGD03GKfu3CVlkkigMetYbRsPdFWQMoK9Gfmku2Bc7850772",
    "token_type": "Bearer",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@example.com",
        "email_verified_at": "2026-01-21T06:14:12.000000Z",
        "role": "admin",
        "created_at": "2026-01-21T06:14:12.000000Z",
        "updated_at": "2026-01-21T06:14:12.000000Z"
    }
}
access token:-
{"access_token": "4|RGD03GKfu3CVlkkigMetYbRsPdFWQMoK9Gfmku2Bc7850772"}
2.logout api:->
<img width="1163" height="538" alt="image" src="https://github.com/user-attachments/assets/65baab78-56db-4144-9985-6ad49bbbcddb" />
link:-> post:->http://127.0.0.1:8000/api/logout
api collection:->
{
    "message": "Logged out successfully"
}
3.user api:->
<img width="1171" height="650" alt="image" src="https://github.com/user-attachments/assets/7d821bd8-dd29-48ab-b99b-18e10cbfc6f3" />
link:->GET:->http://127.0.0.1:8000/api/user
api collection:->
{
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com",
    "email_verified_at": "2026-01-21T06:14:12.000000Z",
    "role": "admin",
    "created_at": "2026-01-21T06:14:12.000000Z",
    "updated_at": "2026-01-21T06:14:12.000000Z"
}
4.Appointment Api:->
<img width="1179" height="746" alt="image" src="https://github.com/user-attachments/assets/202f18bf-893f-4de1-91ac-4556dc2e8aca" />
link:->POST:->http://127.0.0.1:8000/api/appointments
api collection:->
{
    "message": "Appointment request submitted successfully",
    "lead": {
        "id": 2,
        "patient_name": "Sejal Shukla",
        "email": "sejalshukla85@gmail.com",
        "phone": "9876543210",
        "country": "Canada",
        "medical_issue": "Dental Implant",
        "preferred_date": "2026-04-20",
        "status": null,
        "created_at": "2026-01-21T08:43:16+00:00",
        "updated_at": "2026-01-21T08:43:16+00:00"
    }
}
5.Leads api
<img width="1215" height="789" alt="image" src="https://github.com/user-attachments/assets/74f00569-cc5c-4b0d-b5f7-4054d959e2ee" />
link:->GET:->http://127.0.0.1:8000/api/admin/leads
api collection:->
{
    "data": [
        {
            "id": 2,
            "patient_name": "Sejal Shukla",
            "email": "sejalshukla85@gmail.com",
            "phone": "9876543210",
            "country": "Canada",
            "medical_issue": "Dental Implant",
            "preferred_date": "2026-04-20",
            "status": "new",
            "assigned_to": {
                "id": 4,
                "name": "Cordinator Sejal",
                "email": "sejalshukla985@gmail.com"
            },
            "created_at": "2026-01-21T08:43:16+00:00",
            "updated_at": "2026-01-21T08:43:17+00:00"
        },
        {
            "id": 1,
            "patient_name": "sejal shukla",
            "email": "sejalshukla985@gmail.com",
            "phone": "9876543210",
            "country": "Canada",
            "medical_issue": "Dental Implant",
            "preferred_date": "2026-04-20",
            "status": "new",
            "assigned_to": {
                "id": 3,
                "name": "New Coordinator",
                "email": "new.coord@example.com"
            },
            "created_at": "2026-01-21T06:46:28+00:00",
            "updated_at": "2026-01-21T07:42:38+00:00"
        }
    ],
    "links": {
        "first": "http://127.0.0.1:8000/api/admin/leads?page=1",
        "last": "http://127.0.0.1:8000/api/admin/leads?page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "page": null,
                "active": false
            },
            {
                "url": "http://127.0.0.1:8000/api/admin/leads?page=1",
                "label": "1",
                "page": 1,
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "page": null,
                "active": false
            }
        ],
        "path": "http://127.0.0.1:8000/api/admin/leads",
        "per_page": 10,
        "to": 2,
        "total": 2
    }
}
6.Coordinators Api
<img width="1188" height="742" alt="image" src="https://github.com/user-attachments/assets/077bc507-dd93-443f-a7ff-261ca73ac192" />
link:->POST:->http://127.0.0.1:8000/api/admin/coordinators
api collection:->
{
    "message": "Coordinator created successfully",
    "user": {
        "name": "sejal Coordinator",
        "email": "sejal.coord@example.com",
        "role": "coordinator",
        "updated_at": "2026-01-21T08:47:51.000000Z",
        "created_at": "2026-01-21T08:47:51.000000Z",
        "id": 6
    }
}


NOTED:-
1.  GitHub Repository Link: https://github.com/sejalshukl/MedicalLead.git
2.  Setup Instructions**: Define The Above Section.
3.  Database Migrations**: Located in `database/migrations/`. Run `php artisan migrate` to apply.
4.  Architecture Explanation**: See [Architecture Overview](#architecture-overview) section above.
5. Postman Collection / API Manual: See [API Instruction Manual](api_instruction_manual.md) for detailed request/response examples.

