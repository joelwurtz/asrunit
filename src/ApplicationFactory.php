<?php

namespace Asrunit;

use Symfony\Component\Console\Application;
use Symfony\Component\Finder\Finder;

class ApplicationFactory {
    public static function create(): Application {
        $application = new Application('asrunit');
        $finder = new CommandFinder();
        $path = getcwd();

        // Find all potential commands
        $commandMethods = $finder->findCommands($path);

        foreach ($commandMethods as $commandMethod) {
            $application->add($commandMethod);
        }

        return $application;
    }
}