# Searching

---

- [Encrypted Searching](#search)
  - [Pre-requisites](#pre)
  - [Check status](#status)
  - [Re-indexing](#reindex)

Because we have some models with [encrypted data](encryption), you need a solution to be able to search those models. By default, a model without the main data fields encrypted we will use the standard [Laravel Database Query Builder](https://laravel.com/docs/10.x/queries) searching. For models with encrypted fields, we will use [Laravel Scout](https://laravel.com/docs/10.x/scout) and a driver. The easiest being `Collection` but for production should really be something else.

<a name="pre"></a>
### Pre-requisites

- Ensure your `.env` includes the correct `SCOUT_DRIVER=`
- If your scout driver needs to be built, consider doing this in `php artisan deploy:post` task
- [Queues](https://laravel.com/docs/10.x/scout#queueing): ideally queueing is also turned on the setting to make the queue process the scout import.
  - If you're not seeing expected refreshed/updated data in your search, you may need to turn on `php artisan queue:listen`

<a name="status"></a>
### Checking the status of your indexes

Run the following to check the status of your indexes compared to the database.

```bash
php artisan scout:status
```

You ideally want to see `Synchronized` in the "Records difference" column. If not, you will need to re-index. See below:

<a name="reindex"></a>
### Re-indexing

To reimport/rebuild the index for a model, run the following command.

```bash
php artisan scout:import "{model}"
```

Where `{model}` is the path to the class of the model. eg. `App\Models\User`
