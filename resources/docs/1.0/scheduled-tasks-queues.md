# Scheduled Tasks / Queues

---

* [Local](#local)
* [Develop](#develop)

Being a Laravel project naturally we use their documentation as a reference:
- [Queues](https://laravel.com/docs/10.x/queues)
- [Scheduling](https://laravel.com/docs/10.x/scheduling)

---

# Environments

<a name="local"></a>
## Local

### Queues

Some tasks will require the queue to run such as emailing and notifications. Ensure when running locally, you have a terminal open running the queue.

```bash
php artisan queue:listen
```

For a failed queue item; check the database for errors in the `failed_jobs` table. And then run the following after you've fixed the error.

```bash
php artisan queue:retry all
```

Then start the queue again.

### Scheduled tasks

You can add to your `crontab` to set and forget;
```bash
# Edit your crontab
crontab -e

# Within the file add
* * * * * cd ~/<site-path>/<client-domain> && php artisan schedule:run >> /dev/null 2>&1

# Optional - this is to clear out the testing data we use everytime we run the php artisan test
0 1,13 * * * cd ~/<site-path>/client-domain> && php artisan --env=testing --seed migrate:fresh >> /dev/null 2>&1
```

Otherwise run the following when you need the scheduled tasks to run:
```bash
php artisan schedule:run
```

<a name="develop"></a>
## Develop (JB Staging)

### Queues

We will use Supervisor to keep the queues running. The configuration should be in: `/etc/supervisor/conf.d/<client-domain>.conf`

```text
[program:<client-domain>-worker]
command=php8.0 /srv/private/<client-domain>/artisan queue:work --sleep=3 --tries=3
process_name=%(program_name)s_%(process_num)02d
numprocs=2
priority=999
autostart=true
autorestart=true
startsecs=1
startretries=3
user=root
redirect_stderr=true
stdout_logfile=/srv/private/<client-domain>/storage/logs/queue-worker-out.log
stopwaitsecs=600
```

[Further documentation about Laravel Queues in Nuclino](https://app.nuclino.com/Juicebox-Creative/Agency/Laravel-Queues-on-the-Server-93fe47ec-4e18-439c-a2b4-f64e982ac754)

### Scheduled tasks

Ensure we add the schedule task to the `crontab`

```bash
# Edit the www-data crontab
sudo su
runuser -u www-data -- crontab -e

# Within the file add
* * * * * cd /srv/private/<client-domain> && php8.2 artisan schedule:run >> /dev/null 2>&1
```

<larecipe-card shadow>
    <p><strong>Struggling to see that the crontask are running?</strong></p>
    <p>Ensure the <code>storage</code> folder user group/permissions are setup correctly.</p>

<pre class="language-bash">
cd /srv/private/<client-domain>
sudo chown -R juicebox:www-data storage
sudo chmod -R g+rw storage
</pre>
    <p>You should then be able to refer to the <code>storage/logs/commands</code> directory for specific logs</p>
</larecipe-card>
