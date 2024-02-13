# Security measures

---

- [API Token Expiration](#api-token-expiration)
- [API Rate Limiting](#api-rate-limiting)
- [Data encryption](#data-encryption)
- [Automated testing](#automated-testing)
- [UUIDs](#uuids)

Please ensure you have considered the following examples. Some are out of the box, and others need to be added.

<a name="api-token-expiration"></a>
## API Token Expiration

We set how long the API tokens last for from creation (ie. successful login).

**Token expiration: 5hrs (turned off on local)**

## API Token inactivity expiration

We check for inactivity set against a token for the API requests. If there hasn't been a request in X minutes, the token is invalid.

- **Token inactivity expiration: 20 minutes (turned off on local)**
- **Onboarding Token inactivity expiration: 2 hours (turned off on local)**

Note: onboarding scoped tokens have a higher expiration time to aid with their process.

<a name="api-rate-limiting"></a>
## API Rate Limiting Requests

Certain requests should be rate limited in the API. Please ensure you have considered the following example.

- `api` all API routes

### api

- If there is a request user, no limit
- Otherwise, check by IP, and limit to `config('auth.api_rate_limit')` requests per minute (default 60)

<a name="data-encryption"></a>
## Encryt/Decrypt personalised data

See [Encrypt/Decrypt](encryption) notes.

<a name="automated-testing"></a>
## Automated tests

We will add tests to ensure key endpoints are locked down to authorised users.

For more information see [Automated testing](automated-testing) notes.

<a name="uuids"></a>
## UUIDs

We want to avoid using IDs when consuming data in the API. Ensure new models are created with a `uuid` field if they will be used to uniquely identify data via the App. Exception to this is status IDs for models, such as a Deal status. Incremental IDs can be risky for security in the case of an attacker just changing the ID of a request that might not be guarded in order to obtain information they otherwise shouldn't have access to.

See [UUIDs](uuids) notes.
