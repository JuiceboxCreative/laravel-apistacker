# Title of the endpoints section

---

Short summary of the endpoints in this section

* [POST /api/request1](#request1)
* [GET /api/request2](#request1)
* [GET /api/request3](#request1)

<a name="request1">
## Name of Request
---

Description of the request.

```text
POST /api/request1
```

**Parameters:**

| **Parameter** | **Expected** | **Notes** |
| :-            | :-           | :- |
| field1        | string       | Note for field1 |
| field2        | string       | Note for field2     |

### Functionality

Detail the checks ran at this step, and any fields the front end needs to consider when storing or using the returned data.

### Example Responses

**Successful response `200`**

Describe the successful response

```json
{
    "success": true,
    "data" {
        ...
    }
}
```

**Invalid response `422` ie. validation errors**

```json
{
    "message": "The given data was invalid.",
    "errors": {
        "field1": [
            "Please provide field1"
        ],
        "field2": [
            "Please provide field2"
        ]
    }
}
```
