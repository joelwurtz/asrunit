<?php

use Asrunit\Attribute\Task;

#[Task(description: "A simple task that run a bash")]
function bash() {
    \Asrunit\exec('bash', tty: true);
}