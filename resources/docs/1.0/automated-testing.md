# Automated testing #

- [Expectation](#expectation)
- [Backend/API](#backend)
- [Frontend](#frontend)
- [Skipping Tests](#skipping-tests)

To run both the frontend and backend repositories, please make sure you install automated testing. We want you to run the PHP Unit tests to the `pre-push` GIT hook. This will then run the PHPUnit tests (in this case via `php artisan test`) before being able to push your changes to the remote repository. If your application doesn't have a frontend - please ignore the frontend related instructions.

---

<a name="expectation"></a>
## Automated Testing Expectations

### Test driven development

We expect tests to be written for high risk or business critical functionality. Where possible & time allows, add tests for validation, success and fail scenarios. 

---

<a name="backend"></a>
## Backend/API Automated Testing

### Get setup for automated tests on push

If you haven't already, please install the [juicebox/automatedpush](https://bitbucket.org/JuiceBoxCreative/laravel-automatedpush/src/master/) package. 

Using the package `juicebox/automatedpush` we pre-installed for this repository:

```bash
php artisan automatedpush:setup
```

This will publish the file required to `.git/hooks/pre-push`, and that's it!

### Once the frontend is also setup

Check the command in the `.git/hooks/pre-push` file is:

```bash
cd ./ && php artisan dusk && php artisan test
```

So in the GIT push we are saying:

- `php artisan dusk` test the frontend.
- `php artisan test` test the backend/API

### Make sure the file has user executable access:

```bash
chmod u+x .git/hooks/pre-push
```

### Run a new commit and push

Obviously, change a file and then commit it with a descriptive message. Then push.

```bash
git add .
git commit -m "Insert comment here"
git push
```

Provided the script was added correctly, it should now run through the tests. If any failed, it will show you where, and you will need to fix before pushing.

### Manually run tests

This can be done easily by:

```bash
php artisan test
```

### Run a specific test

Handy when you are writing new tests and don't want to wait to test the entire suite of tests; It can be filtered by file names.

```bash
php artisan test --filter=ExampleTest
```

### Test notes

The tests are found in `tests/Feature` and `tests/Unit`. Any file ending in `Test.php` will be run automatically. See [Laravel Testing](https://laravel.com/docs/10.x/testing) for more information.

The environment file for testing is `.env.testing`. It is a good idea to run the database to another database and run it locally.

### Passing data between tests

To run one test and use data from that previous test; you can use `@depends <previous_function_name>` in your comment above the next command.

```php

    /**
     * Test data
     *
     * @return array
     */
    public function test_data(): array
    {
        $data = [
            1,2,3,4,5
        ];
        $this->assertTrue(!empty($data));

        return $data;
    }

    /**
     * Use test data for the test
     *
     * @depends test_data
     * @return void
     */
    public function test_data_exists($data): void
    {
        $this->assertEquals($data, [
            1,2,3,4,5
        ]);
    }
```

<a name="frontend"></a>
## Frontend Automated testing

We will be using [Laravel Dusk](https://laravel.com/docs/10.x/dusk) for testing the frontend via the backend tool. Using this will allow us to have access to backend data to be able to effectively test the user experiences.

### Install dusk

If you haven't run before on your local setup; run the following:

```bash
php artisan dusk:install
```

Be sure to remove 2 test files they automatically add during install: 

- `tests/Browser/ExampleTest.php` 
- `tests/Browser/Pages/HomePage.php`

### Failed tests due to outdated ChromeDriver

Occasionally you might need to upgrade your Chrome Driver; this will happen when you browser updates and you get an error message like this during a test:

```bash
Facebook\WebDriver\Exception\SessionNotCreatedException: session not created: This version of ChromeDriver only supports Chrome version 105
Current browser version is 107.0.5304.87 with binary path /Applications/Google Chrome.app/Contents/MacOS/Google Chrome
```

To upgrade your ChromeDriver:

```bash
php artisan dusk:chrome-driver
```

If that doesn't work you might also need to `composer update` before upgrading your ChromeDriver.

### Get setup for automated tests on push

In the same way we are testing the backend/API copy the `.git/hooks/pre-push` file across from the API and modify the command:

```bash
cd ./ && yarn && CI=true yarn run build && cd ../<client-subdomain>-api && php artisan dusk && cd ../<client-subdomain>-app
```

So in the GIT push we are saying:

- `yarn` ensure we've got the node modules required.
- `CI=true yarn run build` ensure we are building the frontend app in production mode
- `cd ../<client-subdomain>-api` move to the API directory (you can change this to suit your local setup path)
- `php artisan dusk` run the frontend tests
- `cd ../<client-subdomain>-app` move back to the app directory (you can change this to suit your local setup path)

### Test notes

The tests are found in `tests/Browser`. Any file ending in `Test.php` will be run automatically. See [Laravel Dusk](https://laravel.com/docs/10.x/dusk) for more information.

The environment file for testing is `.env.dusk.local`. It is a good idea to run the database to another database and run it locally and you can really just copy `.env.testing`.

### Grouping tests

To be able to filter tests when you want to run a smaller subset; Add the `@group` to the comments above the function

```php

    /**
     * Test description
     * 
     * @group Onboarding
     * @return void
     */
    public function test_onboarding(): void
    {
        ...
    }

    /**
     * Test description
     *
     * @group Onboarding
     * @group Critical
     * @return void
     */
    public function test_another_onboarding(): void
    {
        ...
    }
```

### Run a specific test

Handy when you are writing new tests and don't want to wait to test the entire suite of tests; It can be filtered by group as described above.

```bash
php artisan test --group=Onboarding
```

<a name="skipping-tests"></a>
## Skipping Tests

There are valid cases where you want to skip the automated tests because you might have just ran them and needed to make a small change like a text change. In order to do this easily you can skip the tests when pushing to the remote:

```bash
git push --no-verify
```
