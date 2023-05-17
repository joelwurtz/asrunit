<?php

namespace Castor;

use Castor\Attribute\Task as CommandAttribute;
use Symfony\Component\Console\Command\Command;

class TaskBuilder
{
    public function __construct(private CommandAttribute $commandAttribute, private \ReflectionFunction $function, private ContextRegistry $contextRegistry)
    {
    }

    public function getCommand(): Command
    {
        return new TaskAsCommand($this->commandAttribute, $this->function, $this->contextRegistry);
    }
}