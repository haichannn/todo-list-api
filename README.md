# ✅ To-Do List API

A lightweight, secure REST API for personal task management. Built with Laravel 13 and Laravel Sanctum, this API provides user authentication and full CRUD operations for to-do items with search, pagination, and per-user data isolation.

This project is based on the [Todo List API](https://roadmap.sh/projects/todo-list-api) challenge from [roadmap.sh](https://roadmap.sh).

## 🎯 Project Goals

- **User Authentication** — Registration and token-based authentication via Laravel Sanctum.
- **To-Do Lifecycle** — Full Create, Read, Update, and Delete operations for personal tasks.
- **Data Ownership** — Users can only access and modify their own to-do items, enforced by authorization policies.
- **Input Validation** — Strict validation rules on every mutating request to ensure data integrity.
- **Performance** — Paginated and searchable listing endpoints with database indexing.
- **Security** — Rate limiting on all public and authenticated routes.

## ⭐ Key Features

- Token-based authentication (Bearer tokens)
- Create, list, update, and delete to-do items
- Search todos by title or description
- Configurable pagination (up to 100 items per page)
- Owner-only access control on update and delete
- Rate limiting per route group
- Auto-generated interactive API documentation via [Scramble](https://scramble.dedoc.co/)

## 🛠️ Tech Stack

| Component          | Specification                        |
| :----------------- | :----------------------------------- |
| **Language**        | PHP 8.4                              |
| **Framework**       | Laravel 13                           |
| **Authentication**  | Laravel Sanctum v4 (Bearer Tokens)   |
| **Database**        | SQLite                               |
| **ORM**             | Eloquent                             |
| **API Docs**        | Scramble (OpenAPI / Swagger)          |
| **Testing**         | Pest PHP v4                          |

## 📋 Prerequisites

Before you begin, make sure you have the following installed:

- **PHP** >= 8.4
- **Composer** >= 2.x
- **Node.js** >= 18.x and **npm**
- **SQLite** (usually bundled with PHP)

## 🚀 Installation

1. **Clone the repository**

   ```bash
   git clone <repository-url>
   cd todo-list-api
   ```

2. **Run the setup script**

   The project includes a single setup command that installs all dependencies, generates the app key, runs migrations, and builds frontend assets:

   ```bash
   composer setup
   ```

3. **Start the development server**

   ```bash
   composer run dev
   ```

   This starts the application server at `http://localhost:8000`.

## 🔐 Authentication Flow

This API uses **Bearer Token** authentication powered by Laravel Sanctum. Here is the typical flow for a frontend client:

### 1. Register a New User

Send a `POST` request to `/api/register` with the user's name, email, and password.

```bash
curl -X POST http://localhost:8000/api/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"name": "John Doe", "email": "john@example.com", "password": "Secret1234"}'
```

A successful response returns `201 Created` with the user data.

### 2. Login

Send a `POST` request to `/api/login` with email and password to receive a Bearer token.

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{"email": "john@example.com", "password": "Secret1234"}'
```

Response:

```json
{
  "token": "1|your_bearer_token_here"
}
```

### 3. Use the Token

Include the token in the `Authorization` header for all subsequent requests:

```
Authorization: Bearer 1|your_bearer_token_here
```

### Required Headers

All API requests must include:

| Header           | Value                            | Required |
| :--------------- | :------------------------------- | :------- |
| `Accept`         | `application/json`               | Always   |
| `Content-Type`   | `application/json`               | For requests with a body |
| `Authorization`  | `Bearer <token>`                 | For protected endpoints  |

## 📖 API Documentation

This project uses **Scramble** to auto-generate interactive API documentation from the source code.

When running the application locally, visit:

```
http://localhost:8000/docs/api
```

The documentation includes all endpoints, request/response schemas, validation rules, and authentication requirements. You can also try out API calls directly from the browser.

> **Note:** The API documentation is only available in the `local` environment.

## 📡 API Summary

| Method   | Endpoint              | Auth     | Description                              |
| :------- | :-------------------- | :------- | :--------------------------------------- |
| `POST`   | `/api/register`       | No       | Create a new user account                |
| `POST`   | `/api/login`          | No       | Authenticate and receive a Bearer token  |
| `GET`    | `/api/user`           | Yes      | Get the authenticated user's details     |
| `GET`    | `/api/todos`          | Yes      | List todos (paginated, searchable)       |
| `POST`   | `/api/todos`          | Yes      | Create a new todo                        |
| `PATCH`  | `/api/todos/{todo}`   | Yes      | Update a todo (owner only)               |
| `DELETE` | `/api/todos/{todo}`   | Yes      | Delete a todo (owner only)               |

### Query Parameters for `GET /api/todos`

| Parameter   | Type     | Default | Description                                   |
| :---------- | :------- | :------ | :-------------------------------------------- |
| `search`    | `string` | —       | Filter todos by title or description keyword  |
| `per_page`  | `int`    | `10`    | Number of items per page (max: 100)           |
| `page`      | `int`    | `1`     | Page number                                   |

## ⚠️ Error Response Format

All API error responses follow a consistent JSON structure:

### Validation Error (`422`)

Returned when request data fails validation rules.

```json
{
  "message": "The title field is required.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

### Status Code Reference

| Code  | Meaning                 | When                                              |
| :---- | :---------------------- | :------------------------------------------------ |
| `200` | OK                      | Request succeeded with data                       |
| `201` | Created                 | Resource successfully created                     |
| `204` | No Content              | Resource successfully deleted (empty body)         |
| `401` | Unauthorized            | Missing or invalid Bearer token                   |
| `403` | Forbidden               | Action on a resource you do not own                |
| `404` | Not Found               | Requested resource does not exist                  |
| `422` | Unprocessable Entity    | Validation failed                                  |
| `429` | Too Many Requests       | Rate limit exceeded                                |

### Error Message Examples

| Code  | Example `message`                                        |
| :---- | :------------------------------------------------------- |
| `401` | `"Unauthenticated."`                                     |
| `401` | `"The provided credentials are incorrect."`              |
| `403` | `"You do not have permission to access this resource."`  |
| `404` | `"The requested todo was not found."`                    |
| `429` | `"Too many requests. Please try again later."`           |

> **Tip:** For `429` responses, check the `retry_after` field in the response body to know when you can retry.

## 🚦 Rate Limiting

The API enforces rate limits to protect against abuse:

| Route Group       | Limit                  | Scoped By         |
| :---------------- | :--------------------- | :----------------- |
| **Register**      | 10 requests / minute   | IP address         |
| **Login**         | 5 requests / minute    | IP address + email |
| **API** (general) | 60 requests / minute   | User ID or IP      |

When a rate limit is exceeded, the API returns a `429 Too Many Requests` response.
