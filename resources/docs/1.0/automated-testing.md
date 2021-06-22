To run this repository, please make sure you install automated testing. We want you to run the PHP Unit tests to the `pre-push` GIT hook. This will then run the PHPUnit tests (in this case via `php artisan test`) before being able to push your changes to the remote repository.

# Installation #

Create the following file in `.git/hooks/pre-push`

```php
#!/usr/bin/env php
<?php

echo "Running tests.. ";
exec('cd ./ && php artisan test', $output, $returnCode);

if ($returnCode !== 0) {
  // Show full output
  echo PHP_EOL . implode($output, PHP_EOL) . PHP_EOL;
  echo "Aborting commit.." . PHP_EOL;
  exit(1);
}

// Show summary (last line)
echo array_pop($output) . PHP_EOL;

exit(0);

```

Make sure the file has user executable access:

```bash
chmod u+x .git/hooks/pre-push
```

## Run a new commit and push ##

Obviously, change a file and then commit it with a descriptive message. Then push.

```bash
git add .
git commit -m "Insert comment here"
git push
```

Provided the script was added correctly, it should now run through the tests. If any failed, it will show you where, and you will need to fix before pushing.

## Manually run tests ##

This can be done easily by:

```
php artisan test
```

## Test notes ##

The tests are found in `tests/Feature` and `tests/Unit`. Any file ending in `Test.php` will be run automatically. See [Laravel Testing](https://laravel.com/docs/8.x/testing) for more information.

There is a database schema which is imported to the staging database found in `.env.testing`. This was the initial data we had to work with to test our PHP calculations were correct.
