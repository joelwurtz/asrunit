<?php

namespace Asrunit\Example;

use Asrunit\Attribute\Command;
use Asrunit\Attribute\Description;

#[Command]
#[Description("A simple command that prints bar, but also executes foo")]
function bar() {
    foo();

    echo "bar\n";
}