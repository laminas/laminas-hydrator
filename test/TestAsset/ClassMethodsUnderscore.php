<?php // phpcs:disable WebimpressCodingStandard.NamingConventions.ValidVariableName.NotCamelCapsProperty


declare(strict_types=1);

namespace LaminasTest\Hydrator\TestAsset;

class ClassMethodsUnderscore
{
    /** @var string */
    protected $foo_bar = '1';

    /** @var string */
    protected $foo_bar_baz = '2';

    /** @var bool */
    protected $is_foo = true;

    /** @var bool */
    protected $is_bar = true;

    /** @var bool */
    protected $has_foo = true;

    /** @var bool */
    protected $has_bar = true;

    /** @return string */
    public function getFooBar()
    {
        return $this->foo_bar;
    }

    /** @param string $value */
    public function setFooBar($value): self
    {
        $this->foo_bar = $value;
        return $this;
    }

    /** @return string */
    public function getFooBarBaz()
    {
        return $this->foo_bar_baz;
    }

    /** @param string $value */
    public function setFooBarBaz($value): self
    {
        $this->foo_bar_baz = $value;
        return $this;
    }

    /** @return bool */
    public function getIsFoo()
    {
        return $this->is_foo;
    }

    /** @param bool $value */
    public function setIsFoo($value): self
    {
        $this->is_foo = $value;
        return $this;
    }

    /** @return bool */
    public function isBar()
    {
        return $this->is_bar;
    }

    /** @param bool $value */
    public function setIsBar($value): self
    {
        $this->is_bar = $value;
        return $this;
    }

    /** @return bool */
    public function getHasFoo()
    {
        return $this->has_foo;
    }

    /** @param bool $value */
    public function setHasFoo($value): self
    {
        $this->has_foo = $value;
        return $this;
    }

    /** @return bool */
    public function hasBar()
    {
        return $this->has_bar;
    }

    /** @param bool $value */
    public function setHasBar($value): self
    {
        $this->has_bar = $value;
        return $this;
    }
}
