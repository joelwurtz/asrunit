<?php

namespace Asrunit\Example;

use Asrunit\Attribute\Command;
use Asrunit\Attribute\Description;
use function Asrunit\exec;

#[Command]
#[Description("A simple command that prints foo")]
function foo() {
    echo "foo\n";
}