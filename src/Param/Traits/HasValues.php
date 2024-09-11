<?php

namespace Suyar\ClickHouse\Param\Traits;

use Psr\Http\Message\StreamInterface;

trait HasValues
{
    /**
     * @var array|resource|StreamInterface|string
     */
    protected $values = [];

    protected bool $stringAsFile = false;

    /**
     * @return array|resource|StreamInterface|string
     */
    public function getValues()
    {
        return $this->values;
    }

    public function getStringAsFile(): bool
    {
        return $this->stringAsFile;
    }

    /**
     * @param array|resource|StreamInterface|string $values
     * @return $this
     */
    public function setValues($values, bool $stringAsFile = false): static
    {
        $this->values = $values;
        $this->stringAsFile = $stringAsFile;

        return $this;
    }
}
