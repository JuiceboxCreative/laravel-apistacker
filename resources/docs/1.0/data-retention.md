# Data Retention

Please consider how long and how much data you need to store. Work with the client to make a plan to archive data; particularly any sensitive data.

---

- [Pruning models](#pruning)

<a name="pruning"></a>
## Pruning

Using the methods provided in [Laravel Docs | Eloquent Pruning Models](https://laravel.com/docs/10.x/eloquent#pruning-models) please schedule a daily task to force delete users that were soft deleted older than 6 months ago.
