<?php

namespace App\Actions;

class Npm
{
    public function setup(string $path, bool $enabled): void
    {
        if (! $enabled) {
            return;
        }

        exec('npm install --prefix '.$path);
        exec('npm run lint --prefix '.$path);
        exec('npm run format --prefix '.$path);
        exec('npm run build --prefix '.$path);
    }
}
