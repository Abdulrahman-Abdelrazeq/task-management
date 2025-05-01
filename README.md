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
