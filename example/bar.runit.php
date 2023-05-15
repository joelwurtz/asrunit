<?php

namespace Asrunit\Example;

use Asrunit\Attribute\Task;
use Asrunit\Attribute\Description;

#[Task(description: "A simple task that prints bar, but also executes foo")]
function bar() {
    foo();

    echo "bar\n";
}