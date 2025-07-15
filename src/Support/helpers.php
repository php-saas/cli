<?php

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Composer;
use Illuminate\Support\ProcessUtils;
use Symfony\Component\Process\PhpExecutableFinder;

function delete_files(string $path, array $files): void
{
    foreach ($files as $file) {
        @unlink($path.'/'.$file);
    }
}

function delete_directories(string $path, array $directories): void
{
    $fileSystem = new Filesystem;
    foreach ($directories as $directory) {
        $fileSystem->deleteDirectory($path.'/'.$directory);
    }
}

function composer_binary(): string
{
    $composer = new Composer(new Filesystem);

    return implode(' ', $composer->findComposer());
}

function php_binary(): string
{
    $phpBinary = function_exists('Illuminate\Support\php_binary')
        ? \Illuminate\Support\php_binary()
        : (new PhpExecutableFinder)->find(false);

    return $phpBinary !== false
        ? ProcessUtils::escapeArgument($phpBinary)
        : 'php';
}
