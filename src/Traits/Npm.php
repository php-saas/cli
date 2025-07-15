<?php

namespace PHPSaaS\Cli\Traits;

trait Npm
{
    public function setupNpm(): void
    {
        if ($this->npm === 'no') {
            return;
        }

        $this->runCommands([
            'npm install --prefix '.$this->path,
            'npm run lint --prefix '.$this->path,
            'npm run format --prefix '.$this->path,
            'npm run build --prefix '.$this->path,
        ]);
    }
}
