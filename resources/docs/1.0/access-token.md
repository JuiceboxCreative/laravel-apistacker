# Access Token

---

This is the access token used by Sanctum to gain access to the backend API outside of the frontend application. See [Authentication](/{{route}}/{{version}}/authentication) for more information.

## Endpoints

```text
POST /api/sanctum/token
```

## Request an access token

This request requires a user to provide a valid email/password to retrieve an access token.

```text
POST /api/sanctum/token
```

**Parameters:**

| **Parameter** | **Expected** | **Notes**                      |
| :-            | :-           | :-                             |
| email         | string       | Email of the user              |
| password      | string       | Password of the user           |
| device_name   | string       | The name to describe the token |

**Successful response `200`**

A string with the access token should be returned if the email/password combination was correct.

```json
<access_token>
```

**Invalid response `422` ie. the details were invalid**

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "email": [
            "The provided credentials are incorrect."
        ]
    }
}
```

<larecipe-swagger endpoint="/api/sanctum/token" default-method="post" :default-params="{ 'email': 'web+wesfarmers@juicebox.com.au', 'password': '', 'device_name': 'Docs' }"></larecipe-swagger>
