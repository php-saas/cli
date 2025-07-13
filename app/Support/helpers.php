<?php

function remove_blocks(string $content, string $block): string
{
    return preg_replace(
        '/\/\/ php-saas: '.$block.'.*?\/\/ php-saas: end-'.$block.'/s',
        '',
        $content
    );
}
