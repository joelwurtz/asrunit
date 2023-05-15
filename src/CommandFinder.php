<?php

namespace Asrunit;

use Asrunit\Attribute\Command;
use Symfony\Component\Finder\Finder;

class CommandFinder {

    /** @return CommandFunction[] */
    public function findCommands(string $path): array
    {
        if (\is_file($path)) {
            return $this->doFindCommands([$path]);
        }

        $finder = Finder::create()
            ->files()
            ->name('*.runit.php')
            ->in($path)
        ;

        return $this->doFindCommands($finder);
    }

    /**
     * @param iterable<string|\SplFileInfo> $files
     *
     * @return CommandFunction[]
     *
     * @throws \ReflectionException
     */
    private function doFindCommands(iterable $files): array
    {
        $methods = [];
        $existingFunctions = \get_defined_functions()['user'];

        foreach ($files as $file) {
            $path = $file;
            $name = str_replace('.runit.php', '', $file);

            if ($path instanceof \SplFileInfo) {
                $name = $path->getBasename('.runit.php');
                $path = $path->getRealPath();
            }

            require_once $path;

            $newExistingFunctions = \get_defined_functions()['user'];

            $newFunctions = array_diff($newExistingFunctions, $existingFunctions);
            $existingFunctions = $newExistingFunctions;

            foreach ($newFunctions as $functionName) {
                $reflectionFunction = new \ReflectionFunction($functionName);
                $attributes = $reflectionFunction->getAttributes(Command::class);
                $test = null;

                if (count($attributes) > 0) {
                    $command = new CommandFunction($name, $reflectionFunction);
                    $methods[] = $command;
                }
            }
        }

        return $methods;
    }
}