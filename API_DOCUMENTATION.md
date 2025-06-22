# API Documentation - Learning Management System

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
This API uses Laravel Sanctum for authentication. Most endpoints require a Bearer token.

### Register a new user
**POST** `/api/register`

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "student"
}
```

**Response:**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "student",
            "created_at": "2025-01-01T00:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Login
**POST** `/api/login`

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "student"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### Logout
**POST** `/api/logout`

**Headers:**
```
Authorization: Bearer {token}
```

### Get Profile
**GET** `/api/profile`

**Headers:**
```
Authorization: Bearer {token}
```

## Courses

### Get all courses
**GET** `/api/courses`

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "title": "Introduction to Programming",
            "description": "Learn the basics of programming",
            "code": "CS101",
            "teacher_id": 2,
            "teacher": {
                "id": 2,
                "name": "Dr. Smith",
                "email": "smith@example.com"
            }
        }
    ]
}
```

### Get specific course
**GET** `/api/courses/{id}`

### Create course (Teachers only)
**POST** `/api/courses`

**Request Body:**
```json
{
    "title": "Advanced Programming",
    "description": "Advanced programming concepts",
    "code": "CS201"
}
```

### Update course (Teachers only)
**PUT** `/api/courses/{id}`

### Delete course (Teachers only)
**DELETE** `/api/courses/{id}`

## Assignments

### Get all assignments
**GET** `/api/assignments`

### Get assignments for specific course
**GET** `/api/courses/{courseId}/assignments`

### Get specific assignment
**GET** `/api/assignments/{id}`

### Create assignment (Teachers only)
**POST** `/api/assignments`

**Request Body:**
```json
{
    "title": "Final Project",
    "description": "Create a web application",
    "course_id": 1,
    "due_date": "2025-12-31 23:59:59",
    "max_score": 100
}
```

### Update assignment (Teachers only)
**PUT** `/api/assignments/{id}`

### Delete assignment (Teachers only)
**DELETE** `/api/assignments/{id}`

## Submissions

### Get all submissions
**GET** `/api/submissions`

### Get submissions for specific assignment
**GET** `/api/assignments/{assignmentId}/submissions`

### Get specific submission
**GET** `/api/submissions/{id}`

### Submit assignment (Students only)
**POST** `/api/submissions`

**Request Body:**
```json
{
    "assignment_id": 1,
    "content": "This is my assignment submission..."
}
```

### Update submission
**PUT** `/api/submissions/{id}`

**For Students:**
```json
{
    "content": "Updated assignment content..."
}
```

**For Teachers (Grading):**
```json
{
    "grade": 85,
    "feedback": "Great work! Well done."
}
```

### Delete submission (Students only)
**DELETE** `/api/submissions/{id}`

## Enrollments

### Get all enrollments
**GET** `/api/enrollments`

### Get enrollments for specific course
**GET** `/api/courses/{courseId}/enrollments`

### Get specific enrollment
**GET** `/api/enrollments/{id}`

### Enroll in course (Students only)
**POST** `/api/enrollments`

**Request Body:**
```json
{
    "course_id": 1
}
```

### Update enrollment (Teachers only)
**PUT** `/api/enrollments/{id}`

**Request Body:**
```json
{
    "status": "completed"
}
```

### Drop course (Students only)
**DELETE** `/api/enrollments/{id}`

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation errors",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password must be at least 6 characters."]
    }
}
```

### Unauthorized (401)
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### Forbidden (403)
```json
{
    "success": false,
    "message": "Unauthorized. Only teachers can create courses."
}
```

### Not Found (404)
```json
{
    "success": false,
    "message": "Course not found"
}
```

## Status Codes

- **200**: Success
- **201**: Created
- **400**: Bad Request
- **401**: Unauthorized
- **403**: Forbidden
- **404**: Not Found
- **422**: Validation Error
- **500**: Server Error

## Testing the API

### Using cURL

1. **Register a new user:**
```bash
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "student"
  }'
```

2. **Login:**
```bash
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

3. **Get courses (with token):**
```bash
curl -X GET http://127.0.0.1:8000/api/courses \
  -H "Authorization: Bearer {your_token_here}"
```

### Using Postman

1. Set the base URL to: `http://127.0.0.1:8000/api`
2. For protected routes, add the Authorization header:
   - Type: Bearer Token
   - Token: Your token from login/register response

## Notes

- All timestamps are in ISO 8601 format
- All IDs are integers
- The API uses JSON for all requests and responses
- Content-Type header should be set to `application/json` for POST/PUT requests
- Authentication token should be included in the Authorization header as `Bearer {token}`
- Teachers can only manage their own courses and assignments
- Students can only view and submit to assignments they have access to
- Students can only manage their own submissions and enrollments 
