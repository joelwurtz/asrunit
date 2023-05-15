<?php

namespace Asrunit;

use Asrunit\Attribute\Description;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CommandFunction extends Command
{
    public function __construct(string|null $namespace, private \ReflectionFunction $function)
    {
        $name = strtolower($function->getShortName());

        $commandName = $name;

        if ($namespace) {
            $commandName = $namespace . ':' . $commandName;
        }

        parent::__construct($commandName);
    }

    protected function configure(): void
    {
        $description = '';

        foreach ($this->function->getAttributes(Description::class) as $attribute) {
            $description .= $attribute->newInstance()->description;
        }

        $this->setDescription($description);

        foreach ($this->function->getParameters() as $parameter) {
            $name = strtolower($parameter->getName());

            if ($parameter->isOptional()) {
                $this->addOption($name, null, InputOption::VALUE_OPTIONAL, '', $parameter->getDefaultValue());
            } else {
                $this->addArgument($parameter->getName(), InputArgument::REQUIRED);
            }
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $args = [];

        foreach ($this->function->getParameters() as $parameter) {
            $name = strtolower($parameter->getName());

            if ($parameter->isOptional()) {
                if ($input->hasOption($name)) {
                    $args[] = $input->getOption($name);
                }
            } else {
                $args[] = $input->getArgument($name);
            }
        }

        $result = $this->function->invoke(...$args);

        if ($result === null) {
            return 0;
        }

        if (is_int($result)) {
            return $result;
        }

        return 0;
    }
}