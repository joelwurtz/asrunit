<?php

namespace Asrunit\Attribute;

#[\Attribute(\Attribute::TARGET_FUNCTION | \Attribute::IS_REPEATABLE)]
class Description
{
    public function __construct(
        public string $description
    ) {
    }
}