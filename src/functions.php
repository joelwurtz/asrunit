<?php

declare(strict_types=1);

namespace Asrunit;

use Amp\Process\Process;
use Amp\ByteStream;
use function Amp\async;
use function Amp\Future\await;

function parallel(...$closure): array
{
    $promises = [];
    foreach ($closure as $item) {
        $promises[] = async($item);
    }

    return await($promises);
}

function exec(string|array $command, ?string $workingDirectory = null,
              array $environment = [],
              array $options = []): int
{
    $process = Process::start($command, $workingDirectory, $environment, $options);

    async(fn () => ByteStream\pipe($process->getStdout(), ByteStream\getStdout()));
    async(fn () => ByteStream\pipe($process->getStderr(), ByteStream\getStderr()));

    return $process->join();
}
