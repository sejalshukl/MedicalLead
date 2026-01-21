# Medical Lead & Appointment Management API

A production-ready RESTful API for managing medical leads and appointments, built with Laravel 12.

## 🚀 Features

-   **Authentication**: Secure Token-Based Authentication using Laravel Sanctum.
-   **RBAC**: Role-Based Access Control for Admins and Coordinators.
-   **Lead Management**: Public appointment requests and internal lead tracking.
-   **Auto-Assignment**: Round-Robin algorithm to automatically assign leads to coordinators using Queues.
-   **Notifications**: Email notifications to coordinators upon assignment.
-   **Performance**: Database indexing and pagination implemented.
-   **Architecture**: Service-Repository pattern with Event-Driven design.

## 🛠️ Tech Stack

-   **Framework**: Laravel 12.x
-   **Language**: PHP 8.2+
-   **Database**: MySQL
-   **Queue**: Database Driver
-   **Testing**: PHPUnit / Pest

## ⚙️ Setup Instructions

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

## 🧪 Testing

Run the feature tests to verify the application:

```bash
php artisan test
```

## 🏗️ Architecture Overview

This project follows a **Senior-Level** architecture to ensure scalability and maintainability:

-   **Controllers**: Slim and lightweight. Only handle HTTP requests and responses.
-   **FormRequests**: Handle all validation logic (`StoreLeadRequest`, `UpdateLeadStatusRequest`).
-   **Services**: `LeadService` contains the core business logic.
-   **Events & Listeners**: `LeadCreated` event triggers `AssignLeadListener` to decouple logic.
-   **Jobs**: `AssignLeadJob` handles the background processing of lead assignment.
-   **Resources**: `LeadResource` ensures consistent API response formatting.

## ✅ Compliance Checklist

-   [x] **No Business Logic in Controllers**: Moved to Services.
-   [x] **Queues Implemented**: Used for Lead Assignment.
-   [x] **Validation**: Strict validation using FormRequests.
-   [x] **Tests**: Comprehensive Feature Tests included.
-   [x] **Security**: Sanctum & Middleware used.
