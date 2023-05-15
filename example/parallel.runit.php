<?php

namespace Asrunit\Example;

use Asrunit\Attribute\Task;
use function Asrunit\exec;
use function Asrunit\parallel;

function sleep_5() {
    echo "sleep 5\n";
    exec("sleep 5");

    return "foo";
}

function sleep_7() {
    echo "sleep 7\n";
    exec("sleep 7");

    return "bar";
}

#[Task(description: "A simple task that sleeps for 5 and 7 seconds in parallel")]
function sleep() {
    $start = microtime(true);
    [$foo, $bar] = parallel(fn() => sleep_5(), fn() => sleep_7());
    $end = microtime(true);

    $duration = $end - $start;
    echo "Foo: $foo\n";
    echo "Bar: $bar\n";
    echo "Duration: $duration\n";
}