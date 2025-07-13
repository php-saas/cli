<?php

namespace App\Actions;

use Illuminate\Support\Facades\File;
use RuntimeException;

class Application
{
    public function setup(string $path, string $directory): void
    {
        $this->clone($directory);

        if (! File::exists($path.'/.env')) {
            File::copy($path.'/.env.example', $path.'/.env');
        }

        exec("composer install --working-dir={$path}");

        exec("cd {$path} && php artisan key:generate");
    }

    public function clone(string $directory): void
    {
        if (is_dir($directory)) {
            throw new RuntimeException("Directory {$directory} already exists. Please choose a different name or remove the existing directory.");
        }

        $repositoryUrl = 'git@github.com:php-saas/php-saas.git';

        exec("git clone {$repositoryUrl} {$directory}");
    }

    public function cleanup(string $path): void
    {
        File::deleteDirectory($path.'/.github');
        File::deleteDirectory($path.'/.git');
        File::delete($path.'/.gitignore');
        File::move($path.'/.gitignore.final', $path.'/.gitignore');
    }
}
