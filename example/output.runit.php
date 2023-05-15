<?php

namespace Asrunit\Example;

use Asrunit\Attribute\Task;
use Symfony\Component\Console\Output\OutputInterface;

#[Task(description: "A simple command that use output interface")]
function output(OutputInterface $output) {
    $output->writeln('output from symfony interface');
}
