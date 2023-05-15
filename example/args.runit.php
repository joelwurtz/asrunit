<?php

namespace Asrunit\Example;

use Asrunit\Attribute\Command;
use Asrunit\Attribute\Description;
use function Asrunit\exec;

#[Command]
#[Description("This a command with arguments")]
function args(string $test, int $test2 = 1) {
    exec("echo $test $test2");
}