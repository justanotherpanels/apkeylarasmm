# API Frontend Authentication Documentation

This document describes the API endpoints provided for authenticating and managing user accounts from other websites (third-party client applications).

All request and response payloads use **JSON** format. CSRF protection is disabled for all routes under `/api-frontend/*`.

---

## Base URL
```
http://your-domain.com/api-frontend/auth
```

---

## 1. Register User
Create a new user account. Upon successful registration, a 6-digit OTP code will be sent to the user's phone via WhatsApp. The registered user's connection type (`koneksi`) will automatically be set to `API`.

* **URL**: `/register`
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `full_name` | string | Yes | The user's full name. |
| `username` | string | Yes | Unique alphanumeric username. |
| `email` | string | Yes | Unique valid email address. |
| `country_code`| string | Yes | Country code without '+' (e.g., `62` for Indonesia). |
| `phone` | string | Yes | Phone number (can contain leading `0` or not). |
| `password` | string | Yes | User password (minimum 8 characters). |

### Request Example
```json
{
  "full_name": "John Doe",
  "username": "johndoe",
  "email": "johndoe@example.com",
  "country_code": "62",
  "phone": "8123456789",
  "password": "securepassword123"
}
```

### Response Example (Success - `201 Created`)
```json
{
  "status": "success",
  "message": "Registration successful! Verification code sent to WhatsApp.",
  "user_id": 4
}
```

### Response Example (Validation Error - `422 Unprocessable Content`)
```json
{
  "status": "error",
  "errors": {
    "username": [
      "The username has already been taken."
    ],
    "email": [
      "The email has already been taken."
    ]
  }
}
```

---

## 2. Verify Registration/Login OTP
Verify the 6-digit OTP sent to WhatsApp to activate the user's account.

* **URL**: `/verify-otp`
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `user_id` | integer | Yes | The user's ID returned from the registration response. |
| `otp_code` | string | Yes | The 6-digit verification code received on WhatsApp. |

### Request Example
```json
{
  "user_id": 4,
  "otp_code": "123456"
}
```

### Response Example (Success - `200 OK`)
Activates the account and returns the user profile along with the `api_key` to use for SMM operations or session validation.
```json
{
  "status": "success",
  "message": "Account successfully verified and activated.",
  "user": {
    "id": 4,
    "full_name": "John Doe",
    "username": "johndoe",
    "email": "johndoe@example.com",
    "balance": "0.00",
    "level": "Member",
    "status": "Active",
    "koneksi": "API",
    "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698"
  }
}
```

### Response Example (Error - `400 Bad Request`)
```json
{
  "status": "error",
  "message": "Invalid OTP code."
}
```

---

## 3. Resend OTP
Resend a new 6-digit OTP to the user's WhatsApp number.

* **URL**: `/resend-otp`
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `user_id` | integer | Yes | The user ID to trigger the resend. |

### Request Example
```json
{
  "user_id": 4
}
```

### Response Example (Success - `200 OK`)
```json
{
  "status": "success",
  "message": "A new OTP code has been sent to your WhatsApp."
}
```

---

## 4. Login User
Authenticate an existing user. If the account is not yet verified (status is `Not-Active`), it triggers a new OTP and returns a verification challenge.

* **URL**: `/login`
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `email` | string | Yes | Email address OR Username of the user. |
| `password` | string | Yes | The user's account password. |

### Request Example
```json
{
  "email": "johndoe",
  "password": "securepassword123"
}
```

### Response Example (Success - `200 OK`)
```json
{
  "status": "success",
  "message": "Login successful.",
  "user": {
    "id": 4,
    "full_name": "John Doe",
    "username": "johndoe",
    "email": "johndoe@example.com",
    "balance": "0.00",
    "level": "Member",
    "status": "Active",
    "koneksi": "API",
    "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698"
  }
}
```

### Response Example (Account Unverified Challenge - `403 Forbidden`)
If this is received, the frontend client should redirect the user to the OTP verification screen using the returned `user_id`.
```json
{
  "status": "inactive",
  "message": "Account is not verified. A new OTP has been sent to your WhatsApp number.",
  "user_id": 4
}
```

### Response Example (Error - `401 Unauthorized`)
```json
{
  "status": "error",
  "message": "Invalid credentials."
}
```

---

## 5. Forget Password (Request Reset)
Request a password reset. Sends a 6-digit password reset verification OTP code to the user's WhatsApp phone number.

* **URL**: `/forget`
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `email` | string | Yes | Email address, Username, or registered Phone number. |

### Request Example
```json
{
  "email": "johndoe@example.com"
}
```

### Response Example (Success - `200 OK`)
```json
{
  "status": "success",
  "message": "Password reset verification code has been sent to your WhatsApp.",
  "user_id": 4
}
```

### Response Example (Error - `404 Not Found`)
```json
{
  "status": "error",
  "message": "User not found."
}
```

---

## 6. Reset Password (with OTP)
Reset the user's password using the OTP code received from the `/forget` request.

* **URL**: `/reset-password`
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `user_id` | integer | Yes | The user ID associated with the account. |
| `otp_code` | string | Yes | The 6-digit password reset OTP received on WhatsApp. |
| `password` | string | Yes | The new password (minimum 8 characters). |

### Request Example
```json
{
  "user_id": 4,
  "otp_code": "654321",
  "password": "newsecurepassword456"
}
```

### Response Example (Success - `200 OK`)
```json
{
  "status": "success",
  "message": "Your password has been successfully reset."
}
```

### Response Example (Error - `400 Bad Request`)
```json
{
  "status": "error",
  "message": "Invalid reset OTP code."
}
```

---

## 7. Fetch Dashboard Statistics
Retrieve the authenticated user's core metrics, recent activities (transactions and deposits), and 30-day historical chart data.

* **URL**: `/dashboard` (Relative to `/api-frontend`, i.e., `http://your-domain.com/api-frontend/dashboard`)
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`
  * `Authorization: Bearer <api_key>` (Optional if `api_key` or `key` is passed in request body/query)

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `api_key` | string | No* | The user's active API key. *Required if Authorization header is not provided. |
| `key` | string | No* | Alias for `api_key`. |

### Request Example
```json
{
  "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698"
}
```

### Response Example (Success - `200 OK`)
```json
{
  "status": "success",
  "data": {
    "user": {
      "id": 4,
      "full_name": "John Doe",
      "username": "johndoe",
      "balance": "15000.5000"
    },
    "metrics": {
      "total_order": 2,
      "total_pending": 1,
      "total_success": 1,
      "total_deposit": "500.00"
    },
    "recent_transactions": [
      {
        "id": 12,
        "invoice": "INV-SMM-1",
        "service_name": "Instagram Followers",
        "target": "https://instagram.com/p/1",
        "amount": 100,
        "price": "10.50",
        "status": "Success",
        "created_at": "2026-05-22 19:40:00"
      }
    ],
    "recent_deposits": [
      {
        "id": 5,
        "invoice": "INV-DEP-1",
        "amount": "500.00",
        "status": "Success",
        "created_at": "2026-05-22 19:30:00"
      }
    ],
    "chart_data": [
      {
        "date": "2026-05-21",
        "orders_count": 0,
        "orders_amount": 0.00,
        "deposits_amount": 0.00
      },
      {
        "date": "2026-05-22",
        "orders_count": 2,
        "orders_amount": 30.50,
        "deposits_amount": 500.00
      }
    ]
  }
}
```

### Response Example (Error - `401 Unauthorized`)
```json
{
  "status": "error",
  "message": "Invalid or inactive API key."
}
```

---

## 8. Place SMM Order
Place an SMM order for a service on behalf of the user. The parameters depend on the service's type.

* **URL**: `/order` (Relative to `/api-frontend`, i.e., `http://your-domain.com/api-frontend/order`)
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`
  * `Authorization: Bearer <api_key>` (Optional if `api_key` or `key` is passed in request body/query)

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `api_key` | string | No* | The user's active API key. *Required if Authorization header is not provided. |
| `key` | string | No* | Alias for `api_key`. |
| `id_category_smm` | integer | Yes* | The ID of the SMM Category. *Optional if `service` alias is provided. |
| `id_service_smm` | integer | Yes* | The ID of the SMM Service. *Optional if `service` alias is provided. |
| `service` | string/integer | No* | Alias for `id_service_smm`. Resolves dynamically via service ID or provider's PID. |
| `target` | string | Yes* | The target link or username. *Optional if `link` alias is provided. |
| `link` | string | No* | Alias for `target`. |
| `amount` | integer | Yes* | Number of items to order. *Required for `Default`, `Package`, and `Poll` types (Optional if `quantity` alias is provided). |
| `quantity` | integer | No* | Alias for `amount`. |
| `comments` | string | Yes* | New-line separated comments. *Required only for `Custom Comments` type. |
| `answer_number` | string | Yes* | The poll answer index (e.g. 1, 2). *Required only for `Poll` type. |
| `min` | integer | Yes* | Minimum posts capacity. *Required only for `Subscriptions` type. |
| `max` | integer | Yes* | Maximum posts capacity. *Required only for `Subscriptions` type. |
| `posts` | integer | Yes* | Number of posts. *Required only for `Subscriptions` type. |
| `delay` | integer | Yes* | Delay in minutes (0, 5, 10, 15, 30, 60, 90). *Required only for `Subscriptions` type. |

### Request Example (Default / Package Type)
```json
{
  "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698",
  "id_category_smm": 1,
  "id_service_smm": 5,
  "target": "https://instagram.com/p/123",
  "amount": 500
}
```

### Request Example (cURL Compatibility Format)
```json
{
  "key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698",
  "service": "101",
  "link": "https://instagram.com/p/123",
  "quantity": 500
}
```

### Request Example (Custom Comments Type)
```json
{
  "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698",
  "id_category_smm": 1,
  "id_service_smm": 8,
  "target": "https://instagram.com/p/123",
  "comments": "Nice picture!\nLove this!\nAwesome!"
}
```

### Response Example (Success - `201 Created`)
```json
{
  "status": "success",
  "message": "Order placed successfully!",
  "data": {
    "invoice": "INV-A1B2C3D4E5",
    "order_id": 998877,
    "amount": 500,
    "price": "0.75",
    "balance_remaining": "99.2500",
    "min_order": 10,
    "max_order": 1000,
    "description": "Instagram Likes Service"
  }
}
```

### Response Example (Validation Error - `422 Unprocessable Content`)
```json
{
  "status": "error",
  "errors": {
    "amount": [
      "The amount must be at least 10."
    ]
  }
}
```

### Response Example (Insufficient Balance - `400 Bad Request`)
```json
{
  "status": "error",
  "message": "Insufficient balance to place this order."
}
```

---

## 9. Create Deposit (PayPal & Cryptomus)
Create a new deposit transaction to top up the user's balance. Returns a gateway redirection URL for the user to make a payment.

* **URL**: `/deposit` (Relative to `/api-frontend`, i.e., `http://your-domain.com/api-frontend/deposit`)
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`
  * `Authorization: Bearer <api_key>` (Optional if `api_key` or `key` is passed in request body/query)

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `api_key` | string | No* | The user's active API key. *Required if Authorization header is not provided. |
| `key` | string | No* | Alias for `api_key`. |
| `amount` | numeric | Yes | Deposit amount in USD (minimum is $1). |
| `payment_method` | string | Yes | Payment gateway method. Supported values: `paypal`, `cryptomus`. |
| `url_return` | string | No | Optional client URL to redirect the user to after completing or cancelling the gateway transaction. |
| `url_success` | string | No | Optional client URL to redirect the user to after a successful transaction (overrides `url_return`). |
| `url_cancel` | string | No | Optional client URL to redirect the user to if the transaction is cancelled (overrides `url_return`). |

### Request Example (PayPal)
```json
{
  "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698",
  "amount": 50.00,
  "payment_method": "paypal",
  "url_success": "https://your-client-site.com/deposit/success",
  "url_cancel": "https://your-client-site.com/deposit/cancelled"
}
```

### Response Example (Success - `201 Created`)
```json
{
  "status": "success",
  "message": "PayPal transaction created successfully.",
  "data": {
    "invoice": "DEP-H3K9N1M8S2",
    "payment_method": "Paypal",
    "amount": "50.00",
    "redirect_url": "https://www.sandbox.paypal.com/checkoutnow?token=PAY-XXXXXXXXXXXX"
  }
}
```

### Response Example (Validation Error - `422 Unprocessable Content`)
```json
{
  "status": "error",
  "errors": {
    "amount": [
      "Minimum deposit is $1."
    ],
    "payment_method": [
      "The selected payment method is invalid."
    ]
  }
}
```

---

## 10. Fetch SMM Order History
Retrieve the SMM order history of the authenticated user. Supports pagination and custom filtering by order status or search query (invoice / target).

* **URL**: `/order/history` (Relative to `/api-frontend`, i.e., `http://your-domain.com/api-frontend/order/history`)
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`
  * `Authorization: Bearer <api_key>` (Optional if `api_key` or `key` is passed in request body)

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `api_key` | string | No* | The user's active API key. *Required if Authorization header is not provided. |
| `key` | string | No* | Alias for `api_key`. |
| `limit` | integer | No | Max number of records to return per page (min: 1, max: 100, default: 10). |
| `page` | integer | No | Page number for pagination (default: 1). |
| `status` | string | No | Filter by SMM order status (e.g. `Pending`, `Processing`, `Success`, `Completed`, `Partial`, `Canceled`). Case-insensitive. |
| `search` | string | No | Search query to match against the order `invoice` or `target` URL. |

### Request Example
```json
{
  "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698",
  "limit": 5,
  "page": 1,
  "status": "Pending",
  "search": "INV-ORDER"
}
```

### Response Example (Success - `200 OK`)
```json
{
  "status": "success",
  "data": [
    {
      "id": 14,
      "invoice": "INV-ORDER-BBB",
      "service_name": "Instagram Likes",
      "target": "https://instagram.com/p/bbb",
      "amount": 200,
      "price": "0.30",
      "status": "Pending",
      "remains": 200,
      "start_count": 0,
      "created_at": "2026-05-22 20:00:00"
    }
  ],
  "pagination": {
    "total": 1,
    "per_page": 5,
    "current_page": 1,
    "last_page": 1,
    "from": 1,
    "to": 1
  }
}
```

---

## 11. Fetch Deposit History
Retrieve the deposit transaction history of the authenticated user. Supports pagination and custom filtering by payment status or search query (invoice).

* **URL**: `/deposit/history` (Relative to `/api-frontend`, i.e., `http://your-domain.com/api-frontend/deposit/history`)
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`
  * `Authorization: Bearer <api_key>` (Optional if `api_key` or `key` is passed in request body)

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `api_key` | string | No* | The user's active API key. *Required if Authorization header is not provided. |
| `key` | string | No* | Alias for `api_key`. |
| `limit` | integer | No | Max number of records to return per page (min: 1, max: 100, default: 10). |
| `page` | integer | No | Page number for pagination (default: 1). |
| `status` | string | No | Filter by deposit status (e.g. `Pending`, `Success`, `Cancel`, `Failed`). Case-insensitive. |
| `search` | string | No | Search query to match against the deposit `invoice`. |

### Request Example
```json
{
  "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698",
  "limit": 5,
  "page": 1,
  "status": "Success",
  "search": "INV-DEP"
}
```

### Response Example (Success - `200 OK`)
```json
{
  "status": "success",
  "data": [
    {
      "id": 8,
      "invoice": "INV-DEP-AAA",
      "amount": "50.00",
      "status": "Success",
      "payment_method": "Paypal",
      "created_at": "2026-05-22 19:30:00"
    }
  ],
  "pagination": {
    "total": 1,
    "per_page": 5,
    "current_page": 1,
    "last_page": 1,
    "from": 1,
    "to": 1
  }
}
```

---

## 12. Fetch User Profile
Retrieve the authenticated user's profile information, including their remaining balance (sisa saldo) and active API key.

* **URL**: `/profile` (Relative to `/api-frontend`, i.e., `http://your-domain.com/api-frontend/profile`)
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`
  * `Authorization: Bearer <api_key>` (Optional if `api_key` or `key` is passed in request body)

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `api_key` | string | No* | The user's active API key. *Required if Authorization header is not provided. |
| `key` | string | No* | Alias for `api_key`. |

### Request Example
```json
{
  "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698"
}
```

### Response Example (Success - `200 OK`)
```json
{
  "status": "success",
  "data": {
    "id": 4,
    "full_name": "John Doe",
    "username": "johndoe",
    "email": "johndoe@example.com",
    "country_code": "62",
    "phone": "8123456789",
    "balance": "350.2500",
    "level": "Member",
    "status": "Active",
    "koneksi": "API",
    "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698",
    "created_at": "2026-05-22 19:30:00"
  }
}
```

---

## 13. Update User Password
Update the authenticated user's account password.

* **URL**: `/profile/update-password` (Relative to `/api-frontend`, i.e., `http://your-domain.com/api-frontend/profile/update-password`)
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`
  * `Authorization: Bearer <api_key>` (Optional if `api_key` or `key` is passed in request body)

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `api_key` | string | No* | The user's active API key. *Required if Authorization header is not provided. |
| `key` | string | No* | Alias for `api_key`. |
| `current_password` | string | Yes | The user's current password. |
| `password` | string | Yes | The new password (minimum 8 characters). |
| `password_confirmation` | string | Yes | Confirmation of the new password (must match `password`). |

### Request Example
```json
{
  "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698",
  "current_password": "securepassword123",
  "password": "newsecurepassword123",
  "password_confirmation": "newsecurepassword123"
}
```

### Response Example (Success - `200 OK`)
```json
{
  "status": "success",
  "message": "Password updated successfully."
}
```

### Response Example (Incorrect Current Password - `400 Bad Request`)
```json
{
  "status": "error",
  "message": "Current password is incorrect."
}
```

### Response Example (Validation Error - `422 Unprocessable Content`)
```json
{
  "status": "error",
  "errors": {
    "password": [
      "The password confirmation does not match."
    ]
  }
}
```

---

## 14. Regenerate API Key
Invalidate the current API key and generate a new one.

* **URL**: `/profile/regenerate-api-key` (Relative to `/api-frontend`, i.e., `http://your-domain.com/api-frontend/profile/regenerate-api-key`)
* **Method**: `POST`
* **Headers**:
  * `Content-Type: application/json`
  * `Accept: application/json`
  * `Authorization: Bearer <api_key>` (Optional if `api_key` or `key` is passed in request body)

### Request Parameters

| Parameter | Type | Required | Description |
| :--- | :--- | :--- | :--- |
| `api_key` | string | No* | The user's active API key. *Required if Authorization header is not provided. |
| `key` | string | No* | Alias for `api_key`. |

### Request Example
```json
{
  "api_key": "usr_7e937d57fdf8748981df2f6fbf746b10712a8698"
}
```

### Response Example (Success - `200 OK`)
```json
{
  "status": "success",
  "message": "API key regenerated successfully.",
  "api_key": "usr_5c50c054238e8e7a68894dfc746e5c8e001dfaef"
}
```


