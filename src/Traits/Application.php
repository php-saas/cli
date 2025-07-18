<?php

namespace PHPSaaS\Cli\Traits;

use RuntimeException;

trait Application
{
    protected function setupApplication(): void
    {
        if (is_dir($this->path)) {
            throw new RuntimeException("Directory {$this->path} already exists. Please choose a different name or remove the existing directory.");
        }

        $repositoryUrl = 'git@github.com:php-saas/php-saas.git';

        $this->runCommands([
            "git clone {$repositoryUrl} {$this->tmpPath}",
        ]);

        $this->fileSystem->moveDirectory($this->tmpPath.'/laravel', $this->path);

        if (! file_exists($this->path.'/.env')) {
            $this->fileSystem->copy($this->path.'/.env.example', $this->path.'/.env');
        }

        $this->runCommands([
            composer_binary()." install --working-dir={$this->path}",
            php_binary()." {$this->path}/artisan key:generate",
        ]);
    }

    protected function cleanupApplication(): void
    {
        //
    }
}
