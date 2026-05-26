# Using Hydrators in Forms of laminas-form

[laminas-form](https://docs.laminas.dev/laminas-form/) can [bind objects to forms](https://docs.laminas.dev/laminas-form/v3/quick-start/#binding-an-object), and laminas-hydrator can be used to hydrate and extract data from these objects.

The following examples show _multiple_ potential use cases of laminas-hydrator within laminas-form.

The examples are based on the [tutorial application](https://docs.laminas.dev/tutorials/getting-started/overview/) which builds an album inventory system.

## Adding a Standard Hydrator to a Form Directly

A hydrator can be added directly in a form class itself, using the class name of the hydrator or whatever name the hydrator is registered under.

[Create a form as a separate class](https://docs.laminas.dev/laminas-form/v3/quick-start/#factory-backed-form-extension), define its [`init` method](https://docs.laminas.dev/laminas-form/v3/advanced/#initialization), and set the hydrator via the `setHydratorByName()` method of `Laminas\Form\Form`, e.g. `module/Album/src/Form/AlbumForm.php`:

<pre class="language-php" data-line="11-12"><code>
namespace Album\Form;

use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Form\Element\Text;
use Laminas\Form\Form;

final class AlbumForm extends Form
{
    public function init(): void
    {
        // Set the hydrator
        $this->setHydratorByName(ClassMethodsHydrator::class);

        // Add form elements
        $this->add([
            'name'    => 'title',
            'type'    => Text::class,
            'options' => [
                'label' => 'Title',
            ],
        ]);

        // …
    }
}
</code></pre>

> INFO: **`setHydratorByName` vs. `setHydrator`**
> The example use the `setHydratorByName()` method of `Laminas\Form\Form` instead of `setHydrator` method.
>
> The `setHydratorByName()` method uses the hydrator plugin manager to create an instance of the hydrator.
> This allows usage of any registered and also not registered hydrator with the [hydrators plugin manager](../plugin-managers.md) which includes custom hydrators (with or without a factory) or overwrites of standard hydrators.
>
> The `setHydrator` method of `Laminas\Form\Form` expects an instance of a hydrator and ignores the hydrator plugin manager.

## Adding a Custom Hydrator to a Form Directly

### Create a Custom Hydrator

The following example shows a _rudimentary_ implementation of `Laminas\Hydrator\HydratorInterface` in the file `module/Album/src/Hydrator/AlbumHydrator.php`:

```php
namespace Album\Hydrator;

final class AlbumHydrator implements Laminas\Hydrator\HydratorInterface
{
    public function hydrate(array $data, object $object)
    {
        if (
            array_key_exists('title', $data)
            && is_string($data['title'])
            && $data['title'] !== ''
        ) {
            $object->setTitle($data['title']);
        }
        
        // …
        
        return $object;
    }
    
    public function extract(object $object): array
    {
        return $object->getArrayCopy();
    }
}
```

### Create Form and Add Hydrator

Like before, the custom hydrator can be added directly in a form class itself, using the class name of the hydrator:

<pre class="language-php" data-line="10-11"><code>
namespace Album\Form;

use Album\Hydrator\AlbumHydrator;
use Laminas\Form\Form;

final class AlbumForm extends Form
{
    public function init(): void
    {
        // Set the hydrator
        $this->setHydratorByName(AlbumHydrator::class);

        // Add form elements…
    }
}
</code></pre>

If no separate factory is required for the hydrator, then the hydrator plugin manager will be instantiating the hydrator class without prior registration. Otherwise, the hydrator must be registered.

## Adding Hydrator to Form via Configuration

Forms can be defined via configuration and the hydrator can be added to the form with the `hydrator` key:

<pre class="language-php" data-line="3"><code>
$factory = new Laminas\Form\Factory();
$form    = $factory->createForm([
    'hydrator' => Laminas\Hydrator\ReflectionHydrator::class,
    'elements' => [
        [
            'spec' => [
                'name' => 'title',
                'options' => [
                    'label' => 'Title',
                ],
                'type'  => Laminas\Form\Element\Text::class,
            ],
        ],
        // …
    ],
]);
</code></pre>

More information on using configuration and factory creation can be found in the [laminas-form documentation](https://docs.laminas.dev/laminas-form/v3/form-creation/creation-via-factory/).

## Adding Hydrator to Form via PHP Attribute

If annotations on domain models are used to define the form, then the hydrator can be added to the form via the `Laminas\Form\Annotation\Hydrator` attribute:

<pre class="language-php" data-line="7"><code>
namespace Album\Model;

use Laminas\Filter\StringTrim;
use Laminas\Hydrator\ReflectionHydrator;
use Laminas\Form\Annotation;

#[Annotation\Hydrator(ReflectionHydrator::class)]
final class Album
{
    #[Annotation\Filter(StringTrim::class)]
    #[Annotation\Options(["label" => "Title:"])]
    public string $title;
    
    // …
}
</code></pre>

(DocBlock annotations are also supported.)

More information on using PHP8 attributes or DocBlock annotations can be found in the [laminas-form documentation](https://docs.laminas.dev/laminas-form/v3/form-creation/attributes-or-annotations/).

## Learn More

- [Binding an object to a form](https://docs.laminas.dev/laminas-form/v3/quick-start/#binding-an-object)
- [Hydrator Plugin Managers](../plugin-managers.md)
- [The initialization of elements, fieldsets, and forms of laminas-form](https://docs.laminas.dev/laminas-form/v3/advanced/#initialization)
