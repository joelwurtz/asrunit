<?php

namespace Asrunit\Example;

use Asrunit\Attribute\Task;
use function Asrunit\{exec, cd};

#[Task(description: "A simple command that changes directory")]
function directory() {
    exec(['pwd']);
    cd('../');
    exec(['pwd']);
}