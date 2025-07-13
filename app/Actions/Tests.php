<?php

namespace App\Actions;

use Illuminate\Support\Facades\File;
use RuntimeException;

class Tests
{
    public function setup(string $path, string $stack): void
    {
        $renames = [
            'tests-%s' => 'tests',
        ];

        foreach ($renames as $from => $to) {
            $from = $path.'/'.sprintf($from, strtolower($stack));
            $to = $path.'/'.sprintf($to, strtolower($stack));

            if (! File::exists($from)) {
                throw new RuntimeException("File {$from} does not exist. Please check the template.");
            }

            File::move($from, $to);
        }
    }

    public function cleanup(string $path, string $stack): void
    {
        File::deleteDirectory($path.'/tests-pest');
        File::deleteDirectory($path.'/tests-phpunit');

        switch ($stack) {
            case 'Pest':
                exec('composer remove phpunit/phpunit --no-interaction --working-dir='.$path);
                break;
            case 'PHPUnit':
                exec('composer remove pestphp/pest --no-interaction --working-dir='.$path);
                break;
            default:
        }
    }
}
