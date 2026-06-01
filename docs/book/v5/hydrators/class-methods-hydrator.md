# ClassMethodsHydrator

The ClassMethodsHydrator calls "setter" methods matching keys in the data set to hydrate objects and calls "getter" methods matching keys in the data set during extraction, based on the following rules:

- `is*()`, `has*()`, and `get*()` methods will be used when extracting data.
  The method prefix will be removed from the key's name.
- `set*()` methods will be used when hydrating properties.

```php
class User
{
    private $firstName;
    private $lastName;
    private $emailAddress;
    private $phoneNumber;

    public function setFirstName(string $firstName)
    {
        $this->firstName = $firstName;
    }

    public function setLastName(string $lastName)
    {
        $this->lastName = $lastName;
    }

    public function setEmailAddress(string $emailAddress)
    {
        $this->emailAddress = $emailAddress;
    }

    public function setPhoneNumber(string $phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;
    }
}

$data = [
    'first_name'    => 'James',
    'last_name'     => 'Kahn',
    'email_address' => 'james.kahn@example.org',
    'phone_number'  => '+61 419 1234 5678',
];

$hydrator = new Laminas\Hydrator\ClassMethodsHydrator();
$user     = $hydrator->hydrate($data, new User());
$data     = $hydrator->extract($user);
```
