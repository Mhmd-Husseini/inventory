# Inventory Management API

This is a Laravel 11 API for inventory management with JWT authentication using cookies.

## Setup

1. Clone the repository
2. Install dependencies:
   ```
   composer install
   ```
3. Copy `.env.example` to `.env` and configure your database
4. Generate application key:
   ```
   php artisan key:generate
   ```
5. Generate JWT secret:
   ```
   php artisan jwt:secret
   ```
6. Run migrations:
   ```
   php artisan migrate
   ```
7. Start the server:
   ```
   php artisan serve
   ```

## API Endpoints

### Authentication

#### Register
- **URL**: `/api/auth/register`
- **Method**: `POST`
- **Body**:
  ```json
  {
    "email": "user@example.com",
    "password": "password"
  }
  ```
- **Response**:
  ```json
  {
    "access_token": "token",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "email": "user@example.com",
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  }
  ```

#### Login
- **URL**: `/api/auth/login`
- **Method**: `POST`
- **Body**:
  ```json
  {
    "email": "user@example.com",
    "password": "password"
  }
  ```
- **Response**:
  ```json
  {
    "access_token": "token",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "email": "user@example.com",
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  }
  ```

#### Logout
- **URL**: `/api/auth/logout`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  {
    "message": "Successfully logged out"
  }
  ```

#### Get User
- **URL**: `/api/auth/me`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  {
    "id": 1,
    "email": "user@example.com",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
  ```

#### Refresh Token
- **URL**: `/api/auth/refresh`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  {
    "access_token": "new_token",
    "token_type": "bearer",
    "expires_in": 3600,
    "user": {
      "id": 1,
      "email": "user@example.com",
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  }
  ```

### Product Types

#### List Product Types
- **URL**: `/api/product-types`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  [
    {
      "id": 1,
      "user_id": 1,
      "name": "Product Type 1",
      "description": "Description",
      "current_stocks": 10,
      "image_path": "product_types/image.jpg",
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  ]
  ```

#### Create Product Type
- **URL**: `/api/product-types`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer {token}`
- **Body**:
  ```json
  {
    "name": "Product Type 1",
    "description": "Description",
    "current_stocks": 10,
    "image": "file"
  }
  ```
- **Response**:
  ```json
  {
    "id": 1,
    "user_id": 1,
    "name": "Product Type 1",
    "description": "Description",
    "current_stocks": 10,
    "image_path": "product_types/image.jpg",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
  ```

#### Get Product Type
- **URL**: `/api/product-types/{id}`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  {
    "id": 1,
    "user_id": 1,
    "name": "Product Type 1",
    "description": "Description",
    "current_stocks": 10,
    "image_path": "product_types/image.jpg",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
  ```

#### Update Product Type
- **URL**: `/api/product-types/{id}`
- **Method**: `PUT`
- **Headers**: `Authorization: Bearer {token}`
- **Body**:
  ```json
  {
    "name": "Updated Product Type",
    "description": "Updated Description",
    "current_stocks": 20,
    "image": "file"
  }
  ```
- **Response**:
  ```json
  {
    "id": 1,
    "user_id": 1,
    "name": "Updated Product Type",
    "description": "Updated Description",
    "current_stocks": 20,
    "image_path": "product_types/new_image.jpg",
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
  ```

#### Delete Product Type
- **URL**: `/api/product-types/{id}`
- **Method**: `DELETE`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  {
    "message": "Product type deleted successfully"
  }
  ```

### Items

#### List Items
- **URL**: `/api/items?product_type_id={product_type_id}`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  [
    {
      "id": 1,
      "product_type_id": 1,
      "serial_number": "SN12345",
      "is_sold": false,
      "created_at": "2023-01-01T00:00:00.000000Z",
      "updated_at": "2023-01-01T00:00:00.000000Z"
    }
  ]
  ```

#### Create Item
- **URL**: `/api/items`
- **Method**: `POST`
- **Headers**: `Authorization: Bearer {token}`
- **Body**:
  ```json
  {
    "product_type_id": 1,
    "serial_number": "SN12345",
    "is_sold": false
  }
  ```
- **Response**:
  ```json
  {
    "id": 1,
    "product_type_id": 1,
    "serial_number": "SN12345",
    "is_sold": false,
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
  ```

#### Get Item
- **URL**: `/api/items/{id}`
- **Method**: `GET`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  {
    "id": 1,
    "product_type_id": 1,
    "serial_number": "SN12345",
    "is_sold": false,
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
  ```

#### Update Item
- **URL**: `/api/items/{id}`
- **Method**: `PUT`
- **Headers**: `Authorization: Bearer {token}`
- **Body**:
  ```json
  {
    "serial_number": "SN67890",
    "is_sold": true
  }
  ```
- **Response**:
  ```json
  {
    "id": 1,
    "product_type_id": 1,
    "serial_number": "SN67890",
    "is_sold": true,
    "created_at": "2023-01-01T00:00:00.000000Z",
    "updated_at": "2023-01-01T00:00:00.000000Z"
  }
  ```

#### Delete Item
- **URL**: `/api/items/{id}`
- **Method**: `DELETE`
- **Headers**: `Authorization: Bearer {token}`
- **Response**:
  ```json
  {
    "message": "Item deleted successfully"
  }
  ```

## JWT Cookie Authentication

This API uses JWT tokens stored in cookies for authentication. The token is automatically included in the cookie when you login or register, and is automatically sent with each request. The token is also automatically refreshed when needed.

## Database Schema

### Users
- `id` - bigint, unsigned, auto increment, primary key
- `name` - varchar(255)
- `email` - varchar(255), unique
- `password` - varchar(255)
- `created_at` - timestamp, nullable, default CURRENT_TIMESTAMP
- `updated_at` - timestamp, nullable, default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

### Product Types
- `id` - bigint, unsigned, auto increment, primary key
- `user_id` - bigint, unsigned, foreign key references users(id) ON DELETE CASCADE
- `name` - varchar(255)
- `description` - text, nullable
- `current_stocks` - integer, default 0
- `image_path` - varchar(255), nullable
- `created_at` - timestamp, nullable, default CURRENT_TIMESTAMP
- `updated_at` - timestamp, nullable, default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

### Items
- `id` - bigint, unsigned, auto increment, primary key
- `product_type_id` - bigint, unsigned, foreign key references product_types(id) ON DELETE CASCADE
- `serial_number` - varchar(255), unique
- `is_sold` - tinyint(1), default 0
- `created_at` - timestamp, nullable, default CURRENT_TIMESTAMP
- `updated_at` - timestamp, nullable, default CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
