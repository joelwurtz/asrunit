<?php

namespace Asrunit;

class Context extends \ArrayObject {
    public string $currentDirectory;

    public function __construct() {
        parent::__construct([], \ArrayObject::ARRAY_AS_PROPS);

        $this->currentDirectory = getcwd();
    }
}