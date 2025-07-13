<?php

namespace App\Actions;

class Composer
{
    public function setup(string $path): void
    {
        exec("composer install --working-dir={$path}");
    }
}
