<?php

declare(strict_types=1);

namespace Laminas\Hydrator;

use Laminas\ServiceManager\AbstractSingleInstancePluginManager;
use Laminas\ServiceManager\Exception\InvalidServiceException;
use Laminas\ServiceManager\Factory\InvokableFactory;
use Laminas\ServiceManager\ServiceManager;
use Psr\Container\ContainerInterface;

use function array_replace_recursive;
use function get_debug_type;
use function sprintf;

/**
 * Plugin manager implementation for hydrators.
 *
 * Enforces that adapters retrieved are instances of HydratorInterface
 *
 * @psalm-import-type ServiceManagerConfiguration from ServiceManager
 * @extends AbstractSingleInstancePluginManager<HydratorInterface>
 */
final class HydratorPluginManager extends AbstractSingleInstancePluginManager implements HydratorPluginManagerInterface
{
    private const DEFAULT_CONFIGURATION = [
        'factories' => [
            ArraySerializableHydrator::class => InvokableFactory::class,
            ClassMethodsHydrator::class      => InvokableFactory::class,
            DelegatingHydrator::class        => DelegatingHydratorFactory::class,
            ObjectPropertyHydrator::class    => InvokableFactory::class,
            ReflectionHydrator::class        => InvokableFactory::class,
        ],
        'aliases'   => [
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
        ],
    ];

    /**
     * Whether or not to share by default (v3)
     */
    protected bool $sharedByDefault = false;

    /**
     * {inheritDoc}
     *
     * @var class-string<HydratorInterface>
     */
    protected string $instanceOf = HydratorInterface::class;

    /**
     * @param ServiceManagerConfiguration $config
     */
    public function __construct(ContainerInterface $creationContext, array $config = [])
    {
        /** @var ServiceManagerConfiguration $config */
        $config = array_replace_recursive(self::DEFAULT_CONFIGURATION, $config);
        parent::__construct($creationContext, $config);
    }

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
            get_debug_type($instance),
            HydratorInterface::class
        ));
    }
}
