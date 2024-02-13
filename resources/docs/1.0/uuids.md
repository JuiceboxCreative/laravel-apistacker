# UUIDs

---

For any public facing data, we need to store a `uuid` field for the database record alongside the primary `id` field. This is for security reason to make it harder to guess a record's primary key in order to access it.

We keep the `id` field to make the relationships between the databases easier. But make sure you add a `uuid` field to your table.

## Add field in the migration

Ideally this is done at the start. But we could have a situation that requires it after the fact.

**Field example:**

```php

# For a new table
$table->string('uuid')->unique()->nullable()->index();

# For an alter migration, you can remove the unique flag
$table->string('uuid')->nullable()->index();

```

## Use the Uuid Model Trait

This website uses a Model trait called `Uuid` which can be found in `app/Models/Traits`.

Using `User` model as an example; here is the critical setup:

```php

use App\Models\Traits\Uuid;

...

class User extends Authenticatable
{
    use Uuid;

    ...
}
```

Explaining the setup, we include the trait in the model. Now this will automatically start saving uuids when you add/edit a record.

**Options:**

You can add the following to your model declaration.

```php
    # Field name storing the uuid
    public $uuidFieldName = 'uuid';

    # Whether to use ordered uuid or random uuid
    public $orderedUuid = true;
```
