<?php


namespace Eater;


use Eater\Yaap\Option;

class Yaap
{
    /**
     * @var string[]
     */
    private $argv;

    /**
     * @var string[]
     */
    private $arguments = [];

    /**
     * @var Option[]
     */
    private $options = [];

    /**
     * @var Option[]
     */
    private $optionByName = [];

    public function __construct(array $argv)
    {
        $this->argv = $argv;
    }

    public function addOption(Option $option) {
        $this->options[] = $option;
        $this->indexOption($option);
    }

    public function createOption($type, $name, $aliases = []) {
        $option = new Option();
        $option->setType($type);
        $option->setName($name);
        $option->setAliases($aliases);

        $this->addOption($option);

        return $option;
    }

    public function parse() {
        $args = $this->argv;

        $needsValue = false;
        $currentArg = false;
        $rest = [];

        foreach ($args as $arg) {
            if ($needsValue) {
                $this->getOption($currentArg)->addValue($arg);
                $needsValue = false;
                $currentArg = false;
                continue;
            }

            # quit arg
            if ($arg == '--') {
                break;
            }

            # Long arg
            if (substr($arg, 0 ,2) === '--') {
                $name = substr($arg, 2);
                if (strpos($name, '=') === false) {
                    $option = $this->getOption($name);
                    if ($option === null) {
                        $rest[] = $arg;
                        continue;
                    }

                    switch ($option->getType()) {
                        case Option::FLAG:
                        case Option::OPTIONAL:
                        case Option::INCREMENTING:
                            $option->addValue(true);
                            break;
                        case Option::REQUIRED:
                            $needsValue = true;
                            $currentArg = $name;
                            break;
                    }

                    continue;
                } else {
                    list($name, $value) = explode('=', $name, 2);

                    $option = $this->getOption($name);

                    if ($option === null) {
                        $rest[] = $arg;
                        continue;
                    }

                    switch ($option->getType()) {
                        case Option::FLAG:
                        case Option::INCREMENTING:
                            $option->addValue(true);
                            break;
                        case Option::OPTIONAL:
                        case Option::REQUIRED:
                            $option->addValue($value);
                            break;
                    }

                    continue;
                }
            }


            # Short arg
            if ($arg[0] === '-') {

                $sArgs = substr($arg, 1);

                while (strlen($sArgs) > 0) {
                    $name = $sArgs[0];
                    $sArgs = substr($sArgs, 1);

                    $option = $this->getOption($name);
                    if ($option === null) {
                        $rest[] = $arg;
                        continue 2;
                    }

                    switch ($option->getType()) {
                        case Option::FLAG:
                        case Option::INCREMENTING:
                            $option->addValue(true);
                            continue;
                        case Option::OPTIONAL:
                        case Option::REQUIRED:
                            $next = substr($sArgs, 0, 1);
                            if ($next !== '') {
                                $option->addValue(substr($sArgs, 1));
                                continue 2;
                            } else {
                                if ($option->getType() === Option::OPTIONAL) {
                                    $option->addValue(true);
                                } else {
                                    $needsValue = true;
                                    $currentArg = $name;
                                }

                                break 2;
                            }

                            break;
                    }
                }

                continue;
            }

            # rest
            $rest[] = $arg;
        }

        $this->arguments = $rest;

        return $rest;
    }

    private function indexOption(Option $option) {
        $name = $option->getName();
        if (isset($this->optionByName[$name])) {
            throw new \Exception("There already exists an Option with the name '$name'");
        }
        $this->optionByName[$name] = $option;

        foreach ($option->getAliases() as $alias) {
            if (isset($this->optionByName[$alias])) {
                throw new \Exception("There already exists an Option with the name '$alias'");
            }

            $this->optionByName[$alias] = $option;
        }
    }

    /**
     * @param string $name
     * @return Option|null
     */
    private function getOption($name) {
        return isset($this->optionByName[$name]) ? $this->optionByName[$name] : null;
    }
}