# Implement List To-Do Feature — Phase 4 (Paginated & Filterable)

## Overview
Added `GET /api/todos` endpoint to list authenticated user's to-dos with support for pagination, search, and data isolation.

Resolves #8

## What Has Been Implemented

### 1. Controller Method — `TodoController@index`
Added `index` method to existing controller:
- **User scoping**: Uses `$request->user()->todos()` to ensure data isolation
- **Search filter**: Filters `title` and `description` columns using `when()` with nested closure
- **Pagination**: Defaults 10 items/page, max 100 via `min($request->integer('per_page', 10), 100)`
- **Ordering**: Sorted by newest first using `latest()`
- **Response**: Returns `TodoCollection` for standard `data`, `links`, and `meta` format

### 2. API Route
- Registered `GET /api/todos` in `routes/api.php`
- Placed within existing `auth:sanctum` and `throttle:api` middleware groups

### 3. Rate Limiter Configuration
- Added `api` rate limiter in `AppServiceProvider::boot()`
- Limit 60 requests/minute per authenticated user or guest IP
- Complements existing `register` rate limiter

### 4. Test Suite — `tests/Feature/Todo/ListTodoTest.php`
Implemented 8 test cases:
- ✓ Authenticated user views their todos (validates `data`, `links`, `meta` structure)
- ✓ Unauthenticated user denied access (401)
- ✓ User cannot access other users' todos (data isolation)
- ✓ Pagination with custom `per_page` parameter
- ✓ Default pagination 10 items/page
- ✓ Search filter by `title`
- ✓ Search filter by `description`
- ✓ Empty list returns empty `data` array

### 5. Model Cleanup
- Removed `#[CollectedBy(TodoCollection::class)]` from `Todo` model; attribute was redundant

## API Contract

**Endpoint**: `GET /api/todos`  
**Authentication**: Required (Bearer token via `auth:sanctum`)  
**Rate Limit**: 60 requests/minute

**Query Parameters**:
- `page` (int, optional, default: 1) — Pagination page
- `per_page` (int, optional, default: 10, max: 100) — Items per page
- `search` (string, optional) — Filter by `title` or `description`

**Success Response**: `200 OK`
```json
{
  "data": [
    {
      "id": 1,
      "title": "Learn Laravel",
      "description": "Study Laravel documentation",
      "created_at": "2026-07-30T10:00:00.000000Z",
      "updated_at": "2026-07-30T10:00:00.000000Z"
    }
  ],
  "links": {
    "first": "http://localhost/api/todos?page=1",
    "last": "http://localhost/api/todos?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

**Error Response**: `401 Unauthorized`

## Verification Results

✓ Route registered: `php artisan route:list --path=todos`  
✓ Tests passing: 8 passed (50 assertions) in 4.07s  
✓ No test regressions: All existing tests still pass  
✓ Code formatted: Verified with `vendor/bin/pint --dirty --test`

## Acceptance Criteria
- [x] `TodoController@index` method implemented (under 10 lines)
- [x] Route `GET /api/todos` registered inside existing `auth:sanctum` middleware group
- [x] Only authenticated user's todos are returned (data isolation)
- [x] Pagination works with `page` and `per_page` query params
- [x] Default `per_page` is 10, max is 100
- [x] Search filter works on `title` and `description` fields
- [x] Response uses `TodoCollection` with `data`, `links`, `meta` structure
- [x] 7+ test cases implemented and passing
- [x] Unauthenticated request returns 401
- [x] No existing tests broken
- [x] Code formatted with Pint
- [x] Git branch and commits follow naming conventions
