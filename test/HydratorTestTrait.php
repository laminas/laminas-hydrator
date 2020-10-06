<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator;

use Laminas\Hydrator\NamingStrategy\NamingStrategyInterface;
use Laminas\Hydrator\Strategy\StrategyInterface;
use LaminasTest\Hydrator\TestAsset\SimpleEntity;

use function get_class;
use function sprintf;

trait HydratorTestTrait
{
    public function testHydrateWithNamingStrategyAndStrategy(): void
    {
        $namingStrategy = $this->createMock(NamingStrategyInterface::class);
        $namingStrategy
            ->expects($this->any())
            ->method('hydrate')
            ->with($this->anything())
            ->will($this->returnValue('value'))
        ;

        $strategy = $this->createMock(StrategyInterface::class);
        $strategy
            ->expects($this->any())
            ->method('hydrate')
            ->with($this->anything())
            ->will($this->returnValue('hydrate'))
        ;

        $this->hydrator->setNamingStrategy($namingStrategy);
        $this->hydrator->addStrategy('value', $strategy);

        $entity = $this->hydrator->hydrate(['foo_bar_baz' => 'blub'], new SimpleEntity());
        $this->assertSame(
            'hydrate',
            $entity->getValue(),
            sprintf('Hydrator: %s', get_class($this->hydrator))
        );
    }

    public function testExtractWithNamingStrategyAndStrategy(): void
    {
        $entity = new SimpleEntity();
        $entity->setValue('foo');

        $namingStrategy = $this->createMock(NamingStrategyInterface::class);
        $namingStrategy
            ->expects($this->any())
            ->method('extract')
            ->with($this->anything())
            ->will($this->returnValue('extractedName'));

        $strategy = $this->createMock(StrategyInterface::class);
        $strategy
            ->expects($this->any())
            ->method('extract')
            ->with($this->anything())
            ->will($this->returnValue('extractedValue'));

        $this->hydrator->setNamingStrategy($namingStrategy);
        $this->hydrator->addStrategy('extractedName', $strategy);

        $data = $this->hydrator->extract($entity);

        $this->assertSame(['extractedName' => 'extractedValue'], $data);
    }
}
