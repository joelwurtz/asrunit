<?php

namespace Asrunit;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;

class ApplicationFactory {
    public static function create(): Application {
        $application = new Application('asrunit');
        $contextRegistry = new ContextRegistry();
        $finder = new TaskFinder($contextRegistry);
        $path = getcwd();

        // Find all potential commands / context
        $commandOrContextMethods = $finder->findTasks($path);
        $defaultContext = null;
        $taskBuilders = [];

        foreach ($commandOrContextMethods as $commandOrContextMethod) {
            if ($commandOrContextMethod instanceof TaskBuilder) {
                $taskBuilders[] = $commandOrContextMethod;
                continue;
            }

            if ($commandOrContextMethod instanceof ContextBuilder) {
                if ($commandOrContextMethod->isDefault()) {
                    if ($defaultContext !== null) {
                        throw new \Exception('Only one default context is allowed');
                    }

                    $defaultContext = $commandOrContextMethod;
                    continue;
                }

                $contextRegistry->addContext($commandOrContextMethod->getName(), $commandOrContextMethod);
            }
        }

        $contextRegistry->addContext('default', $defaultContext ?? new ContextBuilder(new Attribute\AsContext(default: true, name: 'default'), new \ReflectionFunction(function() {
            return new Context();
        })));

        foreach ($taskBuilders as $taskBuilder) {
            $application->add($taskBuilder->getCommand());
        }

        return $application;
    }
}