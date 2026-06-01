# ReflectionHydrator

The ReflectionHydrator is similar to the `ObjectPropertyHydrator`, however it uses [PHP's reflection API](http://php.net/manual/en/intro.reflection.php) to hydrate or extract properties of any visibility.
Any data key matching an existing property will be hydrated.
Any existing properties will be used for extracting data.

```php
class User
{
    private $firstName;
    private $lastName;
    private $emailAddress;
    private $phoneNumber;
}

$data = [
    'first_name'    => 'James',
    'last_name'     => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number'  => '+61 419 1234 5678',
];

$hydrator = new Laminas\Hydrator\ReflectionHydrator();
$user     = $hydrator->hydrate($data, new User());
$data     = $hydrator->extract($user);
```
