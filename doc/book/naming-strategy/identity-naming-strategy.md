# IdentityNamingStrategy

`Laminas\Hydrator\NamingStrategy\IdentityNamingStrategy` uses the keys provided to
it for hydration and extraction.

## Basic Usage

```php
$namingStrategy = new Laminas\Hydrator\NamingStrategy\IdentityNamingStrategy();

echo $namingStrategy->hydrate('foo'); // outputs: foo
echo $namingStrategy->extract('bar'); // outputs: bar
```

This strategy can be used in hydrators as well:

```php
class Foo
{
    public $foo;
}

$namingStrategy = new Laminas\Hydrator\NamingStrategy\IdentityNamingStrategy();
$hydrator = new Laminas\Hydrator\ObjectProperty();
$hydrator->setNamingStrategy($namingStrategy);

$foo = new Foo();
$hydrator->hydrate(array('foo' => 123), $foo);

print_r($foo); // Foo Object ( [foo] => 123 )
print_r($hydrator->extract($foo)); // Array ( [foo] => 123 )
```
