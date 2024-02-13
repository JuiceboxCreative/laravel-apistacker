# Authentication

---

We are using Laravel Sanctum [https://laravel.com/docs/10.x/sanctum](https://laravel.com/docs/10.x/sanctum) to provide some protection to the backend API. For API requests via postman or cURL, the majority will need to use an access token via a Bearer token. For the frontend, we will be using CSRF protection via Sanctum and also Bearer token.

## Gaining an access token

Via the `/api/sanctum/token` endpoint you can retrieve the access token required. Once you have the access token, you should add it to the `Authorization: Bearer <access_token>` headers for subsequent requests.

Use the password in 1Password under "Wesfarmers Collection API" > "Website API User".

<larecipe-swagger endpoint="/api/sanctum/token" default-method="post" :default-params="{ 'email': 'web+wesfarmers@juicebox.com.au', 'password': '', 'device_name': 'Docs' }"></larecipe-swagger>

## Access to API endpoints via Postman / cURL

We can run our requests by supplying auth headers with a "Bearer Token" via an access token that we have generated.

### Example cURL request

Get the token from a the sanctum token above, and replace the `<access_token>` in the `Authorization` header.

<larecipe-swagger endpoint="/api/user" default-method="get" :has-auth-header="{ 'Authorization': 'Bearer <access_token>' }"></larecipe-swagger>

Curl example of the above try the following in your terminal, making sure you have a valid `<access_token>`.

```curl
curl https://<api-url>/api/user/ \
-H "Accept: application/json" \
-H "Authorization: Bearer <access_token>"
```

## Unauthenticated requests

**Unauthenticated request `401`**

This is the standard response for an API request without a present or valid Bearer Token.

```json
{
    "message": "Unauthenticated."
}
```

## Frontend access to endpoints

As discussed earlier, we are using Sanctum to provide CSRF protection to the app. Be sure to run the following before any other API requests:

```javascript
//create an axios instance with the withCredentials set to true
const authApi = axios.create({
    withCredentials: true,
    baseURL: process.env.REACT_APP_BASE_URL
});

//or use this line if you plan to use axios directly
axios.defaults.withCredentials = true;

//trigger the sanctum crsf cookie grab
await authApi.get('/sanctum/csrf-cookie');

//set the token received from the previous request
axios.defaults.headers.common = { Authorization: `Bearer ${token}` };

//example post request after we have the correct headers
const response = authApi.post(`${process.env.REACT_APP_BASE_URL}/api/somepostrequest`, {name: 'Test'});
```

After running this request it will set a cookie for `XSRF-TOKEN` and this token now needs to be sent for subsequent requests as a `X-XSRF-TOKEN` header.

Further documentation can be found [https://laravel.com/docs/10.x/sanctum#spa-authenticating](https://laravel.com/docs/10.x/sanctum#spa-authenticating)

## API config/cors.php settings

In the `config/cors.php` file on the Backend API codebase, also be sure to add the `'/sanctum/csrf-cookie'` URL to the `paths` configuration. Eg:

```php
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'sanctum/token'
    ],
```

And in the same file, be sure to add the top level domains we are expecting to call this API for the `allowed_origins` setting. eg.

```php
	'allowed_origins' => [
        '*.<live-domain>',
        '*.dev.juicebox.com.au',
        '*.localhost:*'
        '*.local.box',
        '*.local.box:*'
    ],
```

Also in the file, make sure the `supports_credentials` is also set to `true` to allow the tokens to be generated.

```php
	'supports_credentials' => true,
```

## Bug with XSRF-TOKEN string in cookies

For some reason there are an extra 2 sets of characters `%3D%3D` which is the encoded version of `==` when the browser receives the `XSRF-TOKEN`. This gives a CRSF token mismatch error when it is passed directly to the API because it doesn't get properly decoded. So we need to manually replace them in the frontend action before sending off any POST requests. See the line we provided in the example earlier.

**jQuery example (axios seems to handle this fine)**

```javascript
function getCookie(name){
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

$.ajax({
  url: 'https://<api-url>/sanctum/csrf-cookie',
  xhrFields: { withCredentials: true },
  success: function(data, textStatus, response){
    console.log(response);
  },
  error: function(e){
    console.log(e);
  }
}).then(function(){
  $.ajaxSetup({
    headers: {
      'X-XSRF-TOKEN': getCookie('XSRF-TOKEN').replace(/%3D/g, "=")
    },
    crossDomain: true,
    xhrFields: { withCredentials: true },
  });
  $.ajax({
    method: 'POST',
    url: 'https://<api-url>/api/somepostrequest',
    data: { name: 'Test' },
    success: function(data){
      console.log(data);
    },
    error: function(e){
      console.log(e);
    }
  });
});
```

## Key environment variables

Sanctum needs some special session and domain settings to work for your frontend app. Make sure the following fields are added to your `.env` for your environment:

```env
SESSION_DRIVER=cookie
SESSION_DOMAIN=.dev.juicebox.com.au
SANCTUM_STATEFUL_DOMAINS=<client-subdomain>.dev.juicebox.com.au,<client-api-subdomain>.dev.juicebox.com.au
```
| Variable                      | Notes |
| :-                            | :- |
| `SESSION_DRIVER`              | must be cookie |
| `SESSION_DOMAIN`              | **must be the same root domain** you are calling the API from. ie. on staging, we need to set the staging domain. On live, we will need to set the live domain |
| `SANCTUM_STATEFUL_DOMAINS`    | must be the frontend domains you plan to call the API from |

**Example local environment: (notice the port `:3000` when testing locally)**

```env
SESSION_DRIVER=cookie
SESSION_DOMAIN=.local.box
SANCTUM_STATEFUL_DOMAINS=<client-subdomain>.local.box:3000,<client-api-subdomain>.local.box
```

**Example staging environment:**

```env
SESSION_DRIVER=cookie
SESSION_DOMAIN=.dev.juicebox.com.au
SANCTUM_STATEFUL_DOMAINS=<client-subdomain>.dev.juicebox.com.au,<client-api-subdomain>.dev.juicebox.com.au
```

**Example live environment:**

```env
SESSION_DRIVER=cookie
SESSION_DOMAIN=.<live-domain>
SANCTUM_STATEFUL_DOMAINS=<live-domain>
```
