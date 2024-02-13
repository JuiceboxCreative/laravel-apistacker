# Encryt/Decrypt Personal Data

---

As some of the data is personal and needs to be protected at REST in the database, we need to encrypt/decrypt certain fields.

## Two way vs one way

Anything that will require us to display on the interface will require two way encryption. ie. the data can then be decrypted back to it's original state in order to show the information.

One way is for things such as passwords, which will never need to be shown; only compared against a provided value.

## Encryptable Model Trait

This website uses a Model trait called `Encryptable` which can be found in `app/Models/Traits`. Using `User` model as an example; here is the critical setup:

```php

use App\Models\Traits\Encryptable;

...

class User extends Authenticatable
{
    use Encryptable;

    ...

    /**
     * The attributes that shall be encrypted
     *
     * @var array
     */
    protected $encryptable = [
        'first_name',
        'last_name',
        'mobile'
    ];

    ...
}
```

Explaining the setup, we include the trait in the model, then provide a protected array of fields in the model we wish to encrypt/decrypt.

That's it!

**Please note, you're also welcome to use [Laravel's encrypted casting](https://laravel.com/docs/10.x/eloquent-mutators#encrypted-casting) instead.**

## Displaying data

It's pretty straight forward and minimal if not any code change; Using the normal output methods:

```php
# Grab the Eloquent model
$user = User::first();

# Output the first name
echo $user->first_name;
```

Grabbing an array of values, it will convert the value before adding to the array:

```php
# Grab a list of Eloquent models
$users = User::get()->pluck('first_name','id')->toArray();
foreach($users as $id => $first_name){
    echo $first_name . "\n";
}

```

You can test the above code in Tinker locally to see the model comes back with encrypted values from the database, but then outputs the decrypted value.

```bash
php artisan tinker
```

## Considerations

Searching these fields now becomes impossible via the database; see [searching](searching) documentation for searching encrypted fields.
