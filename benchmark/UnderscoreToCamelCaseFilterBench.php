<?php

namespace LaminasBench\Hydrator;

use Laminas\Hydrator\NamingStrategy\UnderscoreNamingStrategy\UnderscoreToCamelCaseFilter;

class UnderscoreToCamelCaseFilterBench
{
    public function benchFilter()
    {
        $filter = new UnderscoreToCamelCaseFilter();
        for ($i = 0; $i < 1000; ++$i) {
            $filter->filter('test_name');
        }
    }
}