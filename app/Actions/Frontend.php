<?php

namespace App\Actions;

use Illuminate\Support\Facades\File;
use RuntimeException;

class Frontend
{
    public function setup(string $path, string $stack): void
    {
        $renames = [
            'resources/js-%s' => 'resources/js',
            'vite-%s.config.ts' => 'vite.config.ts',
            'tsconfig-%s.json' => 'tsconfig.json',
            'eslint-%s.config.js' => 'eslint.config.js',
            'package-%s.json' => 'package.json',
        ];

        foreach ($renames as $from => $to) {
            $from = $path . '/' . sprintf($from, strtolower($stack));
            $to = $path . '/' . sprintf($to, strtolower($stack));

            if (!File::exists($from)) {
                throw new RuntimeException("File {$from} does not exist. Please check the template.");
            }

            File::move($from, $to);
        }
    }

    public function cleanup(string $path): void
    {
        File::deleteDirectory($path . '/resources/js-vue');
        File::deleteDirectory($path . '/resources/js-react');
    }
}
