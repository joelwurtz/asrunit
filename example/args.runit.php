<?php

namespace Asrunit\Example;

use Asrunit\Attribute\Task;
use function Asrunit\exec;

#[Task(description: "This a task with arguments")]
function args(string $test, int $test2 = 1) {
    exec("echo $test $test2");
}