<?php

namespace PHPSaaS\Cli\Traits;

use RuntimeException;

trait Tests
{
    protected function setupTests(): void
    {
        $renames = [
            'tests-%s' => 'tests',
        ];

        foreach ($renames as $from => $to) {
            $from = $this->path.'/'.sprintf($from, strtolower($this->tests));
            $to = $this->path.'/'.sprintf($to, strtolower($this->tests));

            if (! $this->fileSystem->exists($from)) {
                throw new RuntimeException("File {$from} does not exist. Please check the template.");
            }

            $this->fileSystem->move($from, $to);
        }
    }

    protected function cleanupTests(): void
    {
        $this->fileSystem->deleteDirectory($this->path.'/tests-pest');
        $this->fileSystem->deleteDirectory($this->path.'/tests-phpunit');

        switch ($this->tests) {
            case 'pest':
                $this->runCommands([
                    composer_binary().' remove phpunit/phpunit --no-interaction --working-dir='.$this->path,
                ]);
                break;
            case 'phpunit':
                $this->runCommands([
                    composer_binary().' remove pestphp/pest --no-interaction --working-dir='.$this->path,
                ]);
                break;
            default:
        }
    }
}
