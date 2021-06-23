
# Juicebox Laravel Apistacker

An API starter pack for Laravel & Sanctum

**Requirements:**

* [Laravel 8](https://laravel.com/docs)
* [Laravel Sanctum](https://laravel.com/docs/8.x/sanctum)

### Install the package

To get started, please include the repository VCS reference and then require the

```bash
composer config repositories.apistacker vcs git@bitbucket.org:JuiceBoxCreative/laravel-apistacker.git
composer require juicebox/apistacker --dev
```

### Run the artisan installer

This will publish the package files and append code to the files that need it.

```bash
php artisan apistacker:install
```

### Publish package files

You only need this if you wish to re-publish certain areas. For example a fresh update.

Load all package contents:

```bash
php artisan vendor:publish --tag=apistacker --force
```

Or load individually:

```bash
php artisan vendor:publish --tag=apistacker:postman
php artisan vendor:publish --tag=apistacker:controllers
php artisan vendor:publish --tag=apistacker:docs
```
