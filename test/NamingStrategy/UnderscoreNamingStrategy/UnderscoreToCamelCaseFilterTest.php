<?php

/**
 * @see       https://github.com/laminas/laminas-hydrator for the canonical source repository
 * @copyright https://github.com/laminas/laminas-hydrator/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-hydrator/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace LaminasTest\Hydrator\NamingStrategy\UnderscoreNamingStrategy;

use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy\UnderscoreToCamelCaseFilter;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function extension_loaded;

/**
 * Tests for {@see UnderscoreToCamelCaseFilter}
 *
 * @covers Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy\UnderscoreToCamelCaseFilter
 */
class UnderscoreToCamelCaseFilterTest extends TestCase
{
    /**
     * @dataProvider nonUnicodeProvider
     * @param string $string
     * @param string $expected
     */
    public function testFilterCamelCasesNonUnicodeStrings($string, $expected)
    {
        $filter   = new UnderscoreToCamelCaseFilter();

        $reflectionClass = new ReflectionClass($filter);
        $property = $reflectionClass->getProperty('pcreUnicodeSupport');
        $property->setAccessible(true);
        $property->setValue($filter, false);

        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals($expected, $filtered);
    }

    public function nonUnicodeProvider()
    {
        return [
            'one word' => [
                'Studly',
                'studly'
            ],
            'multiple words' => [
                'studly_cases_me',
                'studlyCasesMe'
            ],
            'alphanumeric in single word' => [
                'one_2_three',
                'one2Three'
            ],
            'alphanumeric in separate words' => [
                'one2_three',
                'one2Three'
            ],
        ];
    }

    /**
     * @dataProvider unicodeProvider
     * @param string $string
     * @param string $expected
     */
    public function testFilterCamelCasesUnicodeStrings($string, $expected)
    {
        if (! extension_loaded('mbstring')) {
            $this->markTestSkipped('Extension mbstring not available');
        }

        $filter   = new UnderscoreToCamelCaseFilter();
        $filtered = $filter->filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals($expected, $filtered);
    }

    public function unicodeProvider()
    {
        return [
            'uppercase first letter' => [
                'Camel',
                'camel'
            ],
            'multiple words' => [
                'studly_cases_me',
                'studlyCasesMe'
            ],
            'alphanumeric in single word' => [
                'one_2_three',
                'one2Three'
            ],
            'alphanumeric in separate words' => [
                'one2_three',
                'one2Three'
            ],
            'unicode character' => [
                'test_Šuma',
                'testŠuma'
            ],
            'unicode character [Laminas-10517]' => [
                'test_šuma',
                'testŠuma'
            ]
        ];
    }

    /**
     * @dataProvider unicodeWithoutMbStringsProvider
     * @param string $string
     * @param string $expected
     */
    public function testFilterCamelCasesUnicodeStringsWithoutMbStrings(
        $string,
        $expected
    ) {

        $filter   = new UnderscoreToCamelCaseFilter();

        $reflectionClass = new ReflectionClass($filter);
        $property = $reflectionClass->getProperty('mbStringSupport');
        $property->setAccessible(true);
        $property->setValue($filter, false);

        $filtered = $filter->filter($string);
        $this->assertEquals($expected, $filtered);
    }

    public function unicodeWithoutMbStringsProvider()
    {
        return [
            'multiple words' => [
                'studly_cases_me',
                'studlyCasesMe'
            ],
            'alphanumeric in single word' => [
                'one_2_three',
                'one2Three'
            ],
            'alphanumeric in separate words' => [
                'one2_three',
                'one2Three'
            ],
            'uppercase unicode character' => [
                'test_Šuma',
                'testŠuma'
            ],
            'lowercase unicode character' => [
                'test_šuma',
                'test_šuma'
            ]
        ];
    }
}
