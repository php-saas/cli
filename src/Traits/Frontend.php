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

        $renames = [
            'resources/js-%s' => 'resources/js',
            'vite-%s.config.ts' => 'vite.config.ts',
            'tsconfig-%s.json' => 'tsconfig.json',
            'eslint-%s.config.js' => 'eslint.config.js',
            'package-%s.json' => 'package.json',
        ];

        foreach ($renames as $from => $to) {
            $from = $this->path.'/'.sprintf($from, strtolower($this->frontend));
            $to = $this->path.'/'.sprintf($to, strtolower($this->frontend));

            if (! $this->fileSystem->exists($from)) {
                throw new RuntimeException("File {$from} does not exist. Please check the template.");
            }

            $this->fileSystem->move($from, $to);
        }
    }

    protected function cleanupFrontend(): void
    {
        $this->fileSystem->deleteDirectory($this->path.'/resources/js-vue');
        $this->fileSystem->deleteDirectory($this->path.'/resources/js-react');
    }
}
