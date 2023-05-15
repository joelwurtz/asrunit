<?php

namespace Asrunit;

use Asrunit\Attribute\Task as CommandAttribute;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TaskAsCommand extends Command
{
    public function __construct(private CommandAttribute $commandAttribute, private \ReflectionFunction $function)
    {
        $commandName = $commandAttribute->name;

        if ($commandAttribute->namespace !== null && $commandAttribute->namespace !== '') {
            $commandName = $commandAttribute->namespace . ':' . $commandName;
        }

        parent::__construct($commandName);
    }

    protected function configure(): void
    {
        $this->setDescription($this->commandAttribute->description);

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