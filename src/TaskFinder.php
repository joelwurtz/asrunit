<?php

namespace Asrunit;

use Asrunit\Attribute\Task;
use Symfony\Component\Finder\Finder;

class TaskFinder {

    /** @return TaskAsCommand[] */
    public function findTasks(string $path): array
    {
        if (\is_file($path)) {
            return $this->doFindTasks([$path]);
        }

        $finder = Finder::create()
            ->files()
            ->name('*.runit.php')
            ->in($path)
        ;

        return $this->doFindTasks($finder);
    }

    /**
     * @param iterable<string|\SplFileInfo> $files
     *
     * @return TaskAsCommand[]
     *
     * @throws \ReflectionException
     */
    private function doFindTasks(iterable $files): array
    {
        $methods = [];
        $existingFunctions = \get_defined_functions()['user'];

        foreach ($files as $file) {
            $path = $file;
            $namespace = str_replace('.runit.php', '', $file);

            if ($path instanceof \SplFileInfo) {
                $namespace = $path->getBasename('.runit.php');
                $path = $path->getRealPath();
            }

            require_once $path;

            $newExistingFunctions = \get_defined_functions()['user'];

            $newFunctions = array_diff($newExistingFunctions, $existingFunctions);
            $existingFunctions = $newExistingFunctions;

            foreach ($newFunctions as $functionName) {
                $reflectionFunction = new \ReflectionFunction($functionName);
                $attributes = $reflectionFunction->getAttributes(Task::class);
                $test = null;

                if (count($attributes) > 0) {
                    $taskAttribute = $attributes[0]->newInstance();

                    if ($taskAttribute->name === '') {
                        $taskAttribute->name = $reflectionFunction->getShortName();
                    }

                    if ($taskAttribute->namespace === null) {
                        $taskAttribute->namespace = $namespace;
                    }

                    $command = new TaskAsCommand($taskAttribute, $reflectionFunction);
                    $methods[] = $command;
                }
            }
        }

        return $methods;
    }
}