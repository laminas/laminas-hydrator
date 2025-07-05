<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\AbstractSingleInstancePluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Factory\InvokableFactory;

use function gettype;
use function is_object;
use function sprintf;

/**
 * Plugin manager implementation for hydrators.
 *
 * Enforces that adapters retrieved are instances of HydratorInterface
 *
 * @extends AbstractPluginManager<HydratorInterface>
 */
class HydratorPluginManager extends AbstractSingleInstancePluginManager implements HydratorPluginManagerInterface
{
    /**
     * Default aliases
     *
     * @inheritDoc
     */
    protected array $aliases = [
        ArraySerializable::class    => ArraySerializableHydrator::class,
        ClassMethods::class         => ClassMethodsHydrator::class,
        ObjectProperty::class       => ObjectPropertyHydrator::class,
        Reflection::class           => ReflectionHydrator::class,
        'arrayserializable'         => ArraySerializableHydrator::class,
        'arraySerializable'         => ArraySerializableHydrator::class,
        'ArraySerializable'         => ArraySerializableHydrator::class,
        'arrayserializablehydrator' => ArraySerializableHydrator::class,
        'arraySerializableHydrator' => ArraySerializableHydrator::class,
        'ArraySerializableHydrator' => ArraySerializableHydrator::class,
        'classmethods'              => ClassMethodsHydrator::class,
        'classMethods'              => ClassMethodsHydrator::class,
        'ClassMethods'              => ClassMethodsHydrator::class,
        'classmethodshydrator'      => ClassMethodsHydrator::class,
        'classMethodsHydrator'      => ClassMethodsHydrator::class,
        'ClassMethodsHydrator'      => ClassMethodsHydrator::class,
        'delegatinghydrator'        => DelegatingHydrator::class,
        'delegatingHydrator'        => DelegatingHydrator::class,
        'DelegatingHydrator'        => DelegatingHydrator::class,
        'objectproperty'            => ObjectPropertyHydrator::class,
        'objectProperty'            => ObjectPropertyHydrator::class,
        'ObjectProperty'            => ObjectPropertyHydrator::class,
        'objectpropertyhydrator'    => ObjectPropertyHydrator::class,
        'objectPropertyHydrator'    => ObjectPropertyHydrator::class,
        'ObjectPropertyHydrator'    => ObjectPropertyHydrator::class,
        'reflection'                => ReflectionHydrator::class,
        'Reflection'                => ReflectionHydrator::class,
        'reflectionhydrator'        => ReflectionHydrator::class,
        'reflectionHydrator'        => ReflectionHydrator::class,
        'ReflectionHydrator'        => ReflectionHydrator::class,

        // Legacy Zend Framework aliases
        'Zend\Hydrator\ArraySerializableHydrator' => ArraySerializableHydrator::class,
        'Zend\Hydrator\ClassMethodsHydrator'      => ClassMethodsHydrator::class,
        'Zend\Hydrator\DelegatingHydrator'        => DelegatingHydrator::class,
        'Zend\Hydrator\ObjectPropertyHydrator'    => ObjectPropertyHydrator::class,
        'Zend\Hydrator\ReflectionHydrator'        => ReflectionHydrator::class,
        'Zend\Hydrator\ArraySerializable'         => ArraySerializableHydrator::class,
        'Zend\Hydrator\ClassMethods'              => ClassMethodsHydrator::class,
        'Zend\Hydrator\ObjectProperty'            => ObjectPropertyHydrator::class,
        'Zend\Hydrator\Reflection'                => ReflectionHydrator::class,
    ];

    /**
     * Default factory-based adapters
     *
     * @inheritDoc
     */
    protected array $factories = [
        ArraySerializableHydrator::class => InvokableFactory::class,
        ClassMethodsHydrator::class      => InvokableFactory::class,
        DelegatingHydrator::class        => DelegatingHydratorFactory::class,
        ObjectPropertyHydrator::class    => InvokableFactory::class,
        ReflectionHydrator::class        => InvokableFactory::class,
    ];

    /**
     * Whether or not to share by default (v3)
     */
    protected bool $sharedByDefault = false;

    /**
     * Whether or not to share by default (v2)
     */
    protected bool $shareByDefault = false;

    /**
     * {inheritDoc}
     *
     * @var class-string<HydratorInterface>|null
     */
    protected string $instanceOf = HydratorInterface::class;

    /**
     * Validate the plugin is of the expected type.
     *
     * Checks that the filter loaded is a valid hydrator.
     *
     * @throws InvalidServiceException
     * @psalm-assert HydratorInterface $instance
     */
    public function validate(mixed $instance): void
    {
        if ($instance instanceof $this->instanceOf) {
            // we're okay
            return;
        }

        throw new InvalidServiceException(sprintf(
            'Plugin of type %s is invalid; must implement %s',
            is_object($instance) ? $instance::class : gettype($instance),
            HydratorInterface::class
        ));
    }
}
