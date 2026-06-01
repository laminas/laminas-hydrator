# ObjectPropertyHydrator

The ObjectPropertyHydrator hydrates objects and extracts data using publicly accessible properties which match a key in the data set.

```php
class User
{
    public $firstName;
    public $lastName;
    public $emailAddress;
    public $phoneNumber;
}

$data = [
    'first_name'    => 'James',
    'last_name'     => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number'  => '+61 419 1234 5678',
];

$hydrator = new Laminas\Hydrator\ObjectPropertyHydrator();
$user     = $hydrator->hydrate($data, new User());
$data     = $hydrator->extract($user);
```
