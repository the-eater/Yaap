<?php


namespace Eater\Yaap;


class Option
{
    const INCREMENTING = 1;
    const REQUIRED = 2;
    const OPTIONAL = 4;
    const FLAG = 8;

    /**
     * @var int
     */
    private $type;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|int
     */
    private $value;

    /**
     * @var string[]|int[]
     */
    private $values = [];

    /**
     * @var string[]
     */
    private $aliases;

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        switch ($this->getType()) {
            case Option::OPTIONAL:
            case Option::REQUIRED:
                return $this->value;
            case Option::FLAG:
                return count($this->getValues()) > 0;
            case Option::INCREMENTING:
                return count($this->getValues());
        }
        return $this->value;
    }

    /**
     * @param int|string $value
     */
    public function addValue($value)
    {
        $this->value = $value;
        $this->values[] = $value;
    }

    /**
     * @return string[]|int[]
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @param string[]|int[] $values
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * @return \string[]
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @param \string[] $aliases
     */
    public function setAliases(array $aliases)
    {
        $this->aliases = $aliases;
    }
}