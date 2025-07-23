<?php

namespace PHPSaaS\Cli\Traits;

trait Frontend
{
    protected function setupFrontend(): void
    {
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

        $block = '@vite([\'resources/js/app.ts\', "resources/js/pages/{$page[\'component\']}.%s"])';
        $this->replaceBlocks(
            [
                'resources/views/app.blade.php',
            ],
            'vite',
            sprintf($block, $this->frontend === 'react' ? 'tsx' : 'vue'),
        );
    }

    protected function cleanupFrontend(): void
    {
        $this->removeBlockTags([
            'resources/views/app.blade.php',
        ], 'vite');
    }
}
