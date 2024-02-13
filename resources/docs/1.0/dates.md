# Dates

---

- [Ouputting dates](#outputting-dates)
    - [Laravel](#laravel)
    - [Frontend](#frontend)

## Database storage of dates

**Dates will be stored in the database in UTC.**

Our databases are based in Australia/Perth so we need to use the `now()->toDateTimeString()` *(which will default to `config('app.timezone')` which is set to 'UTC')* to set a date and not the MySQL `NOW()` function.

<a name="outputting-dates"></a>
## Outputting dates

Ensure when outputting dates, we need to use the user's timezone to convert the date.

<a name="laravel"></a>
### Laravel / API

### User friendly date formats

We have a couple of settings in the `fullstacker.php` config file to output a consistent format across the admin experience.

Dates:

```php
Cleanup::displayUserDate($row->created_at);

//or Manually:
now()->timezone(auth()->user()->timezone)->format(config('fullstacker.date_format_friendly'));
```

Dates with time:

```php
Cleanup::displayUserDateTime($row->created_at);

//or Manually:
now()->timezone(auth()->user()->timezone)->format(config('fullstacker.datetime_format_friendly'));
```

<a name="frontend"></a>
### Frontend

If there is a frontend, we use [moment.js](https://momentjs.com/) to format dates. The library will use the browser's date based on locale. So in order to do it based on the user's profile instead; we need to set the default timezone before showing the user some dates:

```javascript
import moment from 'moment-timezone'
import { DATE_FORMAT } from 'utils/constants'

// Set the timezone globally (replace with user?.user?.timezone if you have the user state)
moment.tz.setDefault('Australia/Perth');

// Then the date from the API would be UTC... and we display in the user's timezone
moment('2022-02-08T05:25:03.000000Z').format(DATE_FORMAT.last_updated) // Tuesday, February 8th 2022, 1:25 pm

```
