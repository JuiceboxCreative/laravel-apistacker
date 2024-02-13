# Artists

---

This will retrieve the artists in the API

* [GET /api/artists](#get-artist)

<a name="get-artist">
## Get artists
---

This will grab a paginated list of artists.

```text
GET /api/artists
```

**Parameters:**

| **Parameter** | **Expected** | **Notes** |
| :-            | :-           | :- |
| letter        | string       | (optional) The letter of the first name to filter by |
| page        | integer       | (optional) The current page of results to be grabbing     |

### Functionality

This will grab the list of artists in the system with the public data available. There is a filter for the artists by the first letter of their first name. The results will be ordered by first name then surname.

### Example Responses

**Successful response `200`**

A successful set of results searching by the letter 'a'

```json
{
    "data": [
        {
            "id": 24,
            "title": "Dr.",
            "first_name": "Abdiel",
            "surname": "Bednar",
            "name": "Abdiel Bednar",
            "other_names": "Efren",
            "nationality": "American",
            "period": "ut"
        },
        {
            "id": 29,
            "title": "Mr.",
            "first_name": "Adele",
            "surname": "Pagac",
            "name": "Adele Pagac",
            "other_names": "Chaya",
            "nationality": "Spanish",
            "period": "consectetur"
        },
        {
            "id": 45,
            "title": "Mrs.",
            "first_name": "Amparo",
            "surname": "Cartwright",
            "name": "Amparo Cartwright",
            "other_names": "Katharina",
            "nationality": "American",
            "period": "rerum"
        },
        {
            "id": 44,
            "title": "Mr.",
            "first_name": "Annalise",
            "surname": "Will",
            "name": "Annalise Will",
            "other_names": "Adela",
            "nationality": "French",
            "period": "consequatur"
        },
        {
            "id": 14,
            "title": "Mrs.",
            "first_name": "Aron",
            "surname": "Braun",
            "name": "Aron Braun",
            "other_names": "Elinor",
            "nationality": "Spanish",
            "period": "dolor"
        },
        {
            "id": 42,
            "title": "Mr.",
            "first_name": "Arvid",
            "surname": "Greenholt",
            "name": "Arvid Greenholt",
            "other_names": "Mr.",
            "nationality": "German",
            "period": "et"
        }
    ],
    "links": {
        "first": "https://<client-api-subdomain>.local.box/api/artists?letter=a&page=1",
        "last": "https://<client-api-subdomain>.local.box/api/artists?letter=a&page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "https://<client-api-subdomain>.local.box/api/artists?letter=a&page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "https://<client-api-subdomain>.local.box/api/artists",
        "per_page": 25,
        "to": 6,
        "total": 6
    }
}
```

**No results `200`**

In this example the letter 'x' returned just an empty `data` variable an a `meta.total` of 0.

```json
{
    "data": [],
    "links": {
        "first": "https://<client-api-subdomain>.local.box/api/artists?letter=x&page=1",
        "last": "https://<client-api-subdomain>.local.box/api/artists?letter=x&page=1",
        "prev": null,
        "next": null
    },
    "meta": {
        "current_page": 1,
        "from": null,
        "last_page": 1,
        "links": [
            {
                "url": null,
                "label": "&laquo; Previous",
                "active": false
            },
            {
                "url": "https://<client-api-subdomain>.local.box/api/artists?letter=x&page=1",
                "label": "1",
                "active": true
            },
            {
                "url": null,
                "label": "Next &raquo;",
                "active": false
            }
        ],
        "path": "https://<client-api-subdomain>.local.box/api/artists",
        "per_page": 25,
        "to": null,
        "total": 0
    }
}
```
