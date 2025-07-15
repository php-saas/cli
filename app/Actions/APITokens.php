<?php

namespace App\Actions;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class APITokens
{
    protected array $filesToDelete = [
        'app/Http/Controllers/TokenController.php',
        'app/Http/Resources/TokenResource.php',
        'tests/Feature/TokenTest.php',
        'database/migrations/2025_07_10_214649_create_personal_access_tokens_table.php',
        'app/Models/PersonalAccessToken.php',
        'app/Policies/PersonalAccessTokenPolicy.php',
        'resources/js/types/token.d.ts',
    ];

    protected array $directoriesToDelete = [
        'resources/js/pages/tokens',
    ];

    protected array $filesToRemoveBlocks = [
        'routes/settings.php',
        'resources/js/layouts/settings/layout.vue',
        'resources/js/layouts/settings/layout.tsx',
        'app/Models/User.php',
    ];

    /**
     * @throws FileNotFoundException
     */
    public function setup(string $path, bool $enabled): void
    {
        if ($enabled) {
            return;
        }

        delete_files($path, $this->filesToDelete);
        delete_directories($path, $this->directoriesToDelete);
        remove_blocks($path, $this->filesToRemoveBlocks, 'tokens');

        // manual removals
        $userModel = File::get($path.'/app/Models/User.php');
        $userModel = str_replace('@property Collection<int, PersonalAccessToken> $tokens', '', $userModel);
        File::put($path.'/app/Models/User.php', $userModel);
    }

    /**
     * @throws FileNotFoundException
     */
    public function cleanup(string $path, bool $enabled): void
    {
        remove_block_tags($path, $this->filesToRemoveBlocks, 'tokens');

        if ($enabled) {
            return;
        }

        exec('composer remove laravel/sanctum --working-dir='.$path);
    }
}
