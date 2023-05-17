<?php

declare(strict_types=1);

namespace Asrunit;

function parallel(...$closure): array
{
    $fibers = [];
    foreach ($closure as $item) {
        $fiber = new \Fiber($item);
        $fiber->start();

        $fibers[] = $fiber;
    }

    $isRunning = true;

    while ($isRunning) {
        $isRunning = false;

        foreach ($fibers as $fiber) {
            $isRunning = $isRunning || !$fiber->isTerminated();

            if (!$fiber->isTerminated() && $fiber->isSuspended()) {
                $fiber->resume();
            }
        }

        if (\Fiber::getCurrent()) {
            \Fiber::suspend();
        }
    }

    return array_map(fn ($fiber) => $fiber->getReturn(), $fibers);
}

function exec(array $command, array $parameters = [], ?string $workingDirectory = null,
                 array $environment = [],
                 array $options = []): int
{
    global $context;

    if ($workingDirectory === null) {
        $workingDirectory = $context->currentDirectory;
    }

    $environment = array_merge($context->environment, $environment);
    $process = new \Symfony\Component\Process\Process($command, $workingDirectory, $environment, null, null);
    $process->setPty(true);
    // $process->setInput(\STDIN); @TODO new to fix a bug in symfony/process in order to use stdin with pty

    $process->start(function ($type, $bytes) {
        if ($type === \Symfony\Component\Process\Process::OUT) {
            fwrite(STDOUT, $bytes);
        } else {
            fwrite(STDERR, $bytes);
        }
    });

    if (\Fiber::getCurrent()) {
        while ($process->isRunning()) {
            \Fiber::suspend();
        }
    }

    return $process->wait();
}

function cd(string $path): void
{
    global $context;

    // if path is absolute
    if (strpos($path, '/') === 0) {
        $context->currentDirectory = $path;
    } else {
        $context->currentDirectory = realpath($context->currentDirectory . '/' . $path);
    }
}
