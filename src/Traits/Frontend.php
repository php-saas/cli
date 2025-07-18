<?php

namespace PHPSaaS\Cli\Traits;

use RuntimeException;

trait Frontend
{
    protected function setupFrontend(): void
    {
        if ($this->frontend === 'vue') {
            throw new RuntimeException('Vue stack is not supported yet.');
        }

        $this->fileSystem->moveDirectory($this->tmpPath.'/'.$this->frontend.'/src', $this->path.'/resources/js');

        $filesToCopy = [
            'vite.config.ts',
            'tsconfig.json',
            'eslint.config.js',
            'package.json',
        ];

        foreach ($filesToCopy as $file) {
            $this->fileSystem->copy($this->tmpPath.'/'.$this->frontend.'/'.$file, $this->path.'/'.$file);
        }
    }

    protected function cleanupFrontend(): void
    {
        //
    }
}
