# 📌 Task Management API — Laravel 10

A RESTful API for managing tasks using Laravel 10, including CRUD operations, filtering, pagination, caching, and Sanctum authentication.

---

### 🚀 Features

-   CRUD operations for tasks
-   Input validation using Form Requests
-   API Resource for structured responses
-   Caching with cache key tracking
-   Soft deletes
-   Filtering, sorting, and pagination
-   API authentication via Laravel Sanctum
-   Unit test for the index endpoint

---

### 📦 Requirements

-   PHP >= 8.1
-   Composer
-   MySQL / PostgreSQL
-   Laravel 10

---

### ⚙️ Installation

1. **Clone the repository**

```bash
git clone https://github.com/Abdulrahman-Abdelrazeq/task-management.git
cd task-management
```

2. **Install dependencies**

```bash
composer install
```

3. **Setup environment**

```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure `.env`**

Update your database and mail settings in the `.env` file.

5. **Run migrations & seeders**

```bash
php artisan migrate --seed
```

6. **Install Sanctum**

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

---

### 🔐 Authentication

Use Sanctum token-based authentication. You can register/login and use the `Bearer` token for API requests.

Example header:

```http
Authorization: Bearer your_token_here
```

---

### 📡 API Endpoints

| Method | Endpoint        | Description                             |
| ------ | --------------- | --------------------------------------- |
| GET    | /api/tasks      | List tasks (with pagination, filtering) |
| POST   | /api/tasks      | Create a new task                       |
| GET    | /api/tasks/{id} | Get a single task                       |
| PATCH  | /api/tasks/{id} | Update a task                           |
| DELETE | /api/tasks/{id} | Soft delete a task                      |

Query parameters for filtering:

```
/api/tasks?keyword=fix&status_id=2&sort_order=name_desc&per_page=10
```

---

### 🔐 Authentication

#### Register

```http
POST /api/register
```

```json
{
    "name": "Abdelrahman Ahmed",
    "email": "abdelrahman@gmail.com",
    "password": "Abdo2024@",
    "password_confirmation": "Abdo2024@"
}
```

Response:

```json
{
    "status": true,
    "message": "User Created Successfully",
    "data": {
        "user": {
            "name": "Abdelrahman Ahmed",
            "email": "abdelrahmaan@gmail.com",
            "updated_at": "2025-05-01 00:59:59",
            "created_at": "2025-05-01 00:59:59",
            "id": 9
        },
        "token": "3|RFKYpGbmdDy8NEtu1FIp9yeURPSyFzmFTiodKQss72320c07",
        "expires_at": "2025-05-08 00:59:59"
    }
}
```

#### Login

```http
POST /api/login
```

```json
{
    "email": "abdelrahman@gmail.com",
    "password": "Abdo2024@"
}
```

Response:

```json
{
    "status": true,
    "message": "Login Successful",
    "data": {
        "user": {
            "id": 8,
            "name": "Abdelrahman Ahmed",
            "email": "abdelrahman@gmail.com",
            "email_verified_at": null,
            "created_at": "2025-05-01 00:43:57",
            "updated_at": "2025-05-01 00:43:57"
        },
        "token": "4|ChTkLfrro7sBg4uhOt55djrGVS5gLzGPL9acxbQa3eb3d8fa",
        "expires_at": "2025-05-08 01:01:13"
    }
}
```

#### Logout

```http
POST /api/logout
Headers: Authorization: Bearer <token>
```

Response:

```json
{
    "status": true,
    "message": "Logged out successfully"
}
```

---

### 📋 Task Endpoints

#### Get All Tasks

```http
GET /api/tasks?per_page=3&keyword=e&sort_order=id_desc
Authorization: Bearer <token>
```

Response:

```json
{
    "status": true,
    "message": "Tasks retrieved successfully",
    "data": [
        {
            "id": 106,
            "name": "A dolor cumque.",
            "description": "Numquam et est dignissimos repellendus corrupti possimus aut. Iusto sequi blanditiis quam nobis incidunt pariatur. Omnis odit tempore quia cupiditate fuga.",
            "status": {
                "id": 22,
                "name": "Pending"
            },
            "created_at": "2025-05-01 00:44:05",
            "updated_at": "2025-05-01 00:44:05"
        },
        {
            "id": 105,
            "name": "Aut natus voluptatem iste.",
            "description": "Omnis nobis rem laborum aut atque. Dicta excepturi nihil deserunt est velit dolorem aut. In accusantium eveniet minus est.",
            "status": {
                "id": 23,
                "name": "In_progress"
            },
            "created_at": "2025-05-01 00:44:05",
            "updated_at": "2025-05-01 00:44:05"
        },
        {
            "id": 104,
            "name": "Qui culpa explicabo ut.",
            "description": "Aut neque corrupti in aut officiis. Eius beatae quaerat laboriosam accusamus facilis porro. Est ut excepturi enim cupiditate ipsa sed delectus sed. Molestias enim officiis laudantium natus ut rerum illum. Maxime veritatis excepturi ut impedit.",
            "status": {
                "id": 24,
                "name": "Completed"
            },
            "created_at": "2025-05-01 00:44:05",
            "updated_at": "2025-05-01 00:44:05"
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 17,
        "per_page": 3,
        "total": 50,
        "next_page_url": "http://127.0.0.1:8000/api/tasks?page=2",
        "prev_page_url": null
    }
}
```

#### Get One Task

```http
GET /api/tasks/1
Authorization: Bearer <token>
```

Response:

```json
{
    "status": true,
    "message": "Task retrieved successfully",
    "data": {
        "id": 104,
        "name": "Qui culpa explicabo ut.",
        "description": "Aut neque corrupti in aut officiis. Eius beatae quaerat laboriosam accusamus facilis porro. Est ut excepturi enim cupiditate ipsa sed delectus sed. Molestias enim officiis laudantium natus ut rerum illum. Maxime veritatis excepturi ut impedit.",
        "status": {
            "id": 24,
            "name": "Completed"
        },
        "created_at": "2025-05-01 00:44:05",
        "updated_at": "2025-05-01 00:44:05"
    }
}
```

#### Create Task

```http
POST /api/tasks
Authorization: Bearer <token>
```

```json
{
    "name": "Test",
    "description": "Test Desc",
    "status_id": 2
}
```

Response:

```json
{
    "status": true,
    "message": "Task created successfully"
}
```

#### Update Task

```http
PATCH /api/tasks/{id}
Authorization: Bearer <token>
```

```json
{
    "name": "Updated Task Name",
    "description": "Updated description"
}
```

Response:

```json
{
    "status": true,
    "message": "Task updated successfully",
    "data": {
        "id": 104,
        "name": "Qui culpa explicabo ut.",
        "description": "Aut neque corrupti in aut officiis. Eius beatae quaerat laboriosam accusamus facilis porro. Est ut excepturi enim cupiditate ipsa sed delectus sed. Molestias enim officiis laudantium natus ut rerum illum. Maxime veritatis excepturi ut impedit.",
        "status": {
            "id": 24,
            "name": "Completed"
        },
        "created_at": "2025-05-01 00:44:05",
        "updated_at": "2025-05-01 00:44:05"
    }
}
```

#### Delete Task (Soft Delete)

```http
DELETE /api/tasks/{id}
Authorization: Bearer <token>
```

Response:

```json
{
    "status": true,
    "message": "Task deleted successfully"
}
```

---

### 🧪 Running Tests

```bash
php artisan test
```

Includes:

-   Unit test for `GET /api/tasks` with and without authentication

---

### 🧼 Code Style & Best Practices

-   Service layer for caching logic
-   Form Request classes for validation
-   API Resource for consistent JSON formatting
-   Proper error handling and logging
-   Clean and readable controller structure

---

### 🛠️ Contribution

Pull requests are welcome! Follow PSR-12 and Laravel conventions.

---
