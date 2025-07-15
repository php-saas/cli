<?php

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

/**
 * @throws FileNotFoundException
 */
function remove_blocks(string $path, array $files, string $block): int
{
    $removed = 0;

    foreach ($files as $file) {
        $filePath = $path.'/'.$file;

        if (! File::exists($filePath)) {
            continue;
        }

        $content = File::get($filePath);

        $patterns = [
            // PHP comments
            "/\/\/ <php-saas:{$block}>.*?\/\/ <\/php-saas:{$block}>\n?/s",
            // JSX/TSX comments
            "/\{\s*\/\*<php-saas:{$block}>\*\/\s*\}.*?\{\s*\/\*<\/php-saas:{$block}>\*\/\s*\}\n?/s",
            // HTML comments
            "/<!--<php-saas:{$block}>-->.*?<!--<\/php-saas:{$block}>-->\n?/s",
            // .env comments
            "/# <php-saas:{$block}>.*?# <\/php-saas:{$block}>\n?/s",
        ];

        $newContent = $content;

        foreach ($patterns as $pattern) {
            $newContent = preg_replace($pattern, '', $newContent, -1, $count);
            $removed += $count;
        }

        if ($newContent !== $content) {
            File::put($filePath, $newContent);
        }
    }

    return $removed;
}

/**
 * @throws FileNotFoundException
 */
function remove_block_tags(string $path, array $files, string $block): int
{
    $tagsRemoved = 0;

    foreach ($files as $file) {
        $filePath = $path.'/'.$file;

        if (! File::exists($filePath)) {
            continue;
        }

        $content = File::get($filePath);

        $patterns = [
            // PHP comments
            "/^\s*\/\/ <\/?php-saas:{$block}>\s*[\r\n]?/m",
            // JSX/TSX comments
            "/^\s*\{\s*\/\*<\\/?php-saas:{$block}>\*\/\s*\}\s*[\r\n]?/m",
            // HTML comments
            "/^\s*<!--<\\/?php-saas:{$block}>-->\s*[\r\n]?/m",
            // .env comments
            "/^\s*# <\/?php-saas:{$block}>\s*[\r\n]?/m",
        ];

        $newContent = $content;

        foreach ($patterns as $pattern) {
            $newContent = preg_replace($pattern, '', $newContent, -1, $count);
            $tagsRemoved += $count;
        }

        if ($newContent !== $content) {
            File::put($filePath, $newContent);
        }
    }

    return $tagsRemoved;
}

function delete_files(string $path, array $files): void
{
    foreach ($files as $key => $file) {
        $files[$key] = $path.'/'.$file;
    }

    File::delete($files);
}

function delete_directories(string $path, array $directories): void
{
    foreach ($directories as $key => $directory) {
        File::deleteDirectory($path.'/'.$directory);
    }
}
