<?php

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\ClassMethodsHydrator;
use Laminas\Hydrator\ConstructorParametersHydratorDecorator;
use Laminas\Hydrator\ProxyObject;
use LaminasTest\Hydrator\TestAsset\ObjectWithConstructor;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use TypeError;

final class ConstructorParametersHydratorDecoratorTest extends TestCase
{
    public function testWithAllParametersPresent(): void
    {
        $data    = [
            'foo'         => 'bar',
            'bar'         => 99,
            'isMandatory' => true,
            'price'       => 19.98,
        ];
        $subject = new ConstructorParametersHydratorDecorator(new ClassMethodsHydrator(false));
        $object  = $subject->hydrate($data, new ProxyObject(ObjectWithConstructor::class));

        Assert::assertInstanceOf(ObjectWithConstructor::class, $object);
        Assert::assertEquals(new ObjectWithConstructor('bar', true, 19.98, 99), $object);
    }

    public function testWithWrongScalarType(): void
    {
        $data    = [
            'foo'         => 123,
            'bar'         => '99',
            'isMandatory' => 1,
            'price'       => '19.98',
        ];
        $subject = new ConstructorParametersHydratorDecorator(new ClassMethodsHydrator(false));
        $object  = $subject->hydrate($data, new ProxyObject(ObjectWithConstructor::class));

        Assert::assertInstanceOf(ObjectWithConstructor::class, $object);
        Assert::assertEquals(new ObjectWithConstructor('123', true, 19.98, 99), $object);
    }

    public function testWithAdditionalSetter(): void
    {
        $data    = [
            'foo'         => 'bar',
            'bar'         => 99,
            'isMandatory' => true,
            'price'       => 19.98,
            'baz'         => 'Hello world',
        ];
        $subject = new ConstructorParametersHydratorDecorator(new ClassMethodsHydrator(false));
        $object  = $subject->hydrate($data, new ProxyObject(ObjectWithConstructor::class));

        Assert::assertInstanceOf(ObjectWithConstructor::class, $object);
        Assert::assertEquals(
            (new ObjectWithConstructor('bar', true, 19.98, 99))->setBaz('Hello world'),
            $object
        );
    }

    public function testWithMissingOptionalParameter(): void
    {
        $data    = [
            'foo'         => 'bar',
            'isMandatory' => true,
            'price'       => 19.98,
        ];
        $subject = new ConstructorParametersHydratorDecorator(new ClassMethodsHydrator(false));
        $object  = $subject->hydrate($data, new ProxyObject(ObjectWithConstructor::class));

        Assert::assertInstanceOf(ObjectWithConstructor::class, $object);
        Assert::assertEquals(new ObjectWithConstructor('bar', true, 19.98, 42), $object);
    }

    public function testWithMissingMandatoryParameter(): void
    {
        $data    = [];
        $subject = new ConstructorParametersHydratorDecorator(new ClassMethodsHydrator(false));

        $this->expectException(TypeError::class);
        $subject->hydrate($data, new ProxyObject(ObjectWithConstructor::class));
    }

    public function testExtract(): void
    {
        $subject = new ConstructorParametersHydratorDecorator(new ClassMethodsHydrator(false));
        $data    = $subject->extract(new ObjectWithConstructor('bar', true, 19.98, 99));
        Assert::assertSame(
            [
                'foo'         => 'bar',
                'isMandatory' => true,
                'price'       => 19.98,
                'bar'         => 99,
                'baz'         => null,
            ],
            $data
        );
    }
}
