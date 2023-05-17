<?php

namespace Asrunit\Example;

use Asrunit\Attribute\AsContext;
use Asrunit\Attribute\Task;
use Asrunit\Context;
use function Asrunit\exec;

#[AsContext(name: 'context_env')]
function context_env(): Context {
    return new Context(environment: [
        'FOO' => 'toto',
    ]);
}

#[Task(description: "A simple task that use environment variables")]
function env(Context $context) {
    exec(['echo', '$FOO', '$BAR'], environment: ['BAR' => 'tata']);
}
