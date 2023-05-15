<?php

namespace Asrunit;

use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;

class ApplicationFactory {
    public static function create(): Application {
        $application = new Application('asrunit');
        $finder = new TaskFinder();
        $path = getcwd();

        // Find all potential commands
        $commandMethods = $finder->findTasks($path);

        foreach ($commandMethods as $commandMethod) {
            $application->add($commandMethod);
        }

        return $application;
    }
}