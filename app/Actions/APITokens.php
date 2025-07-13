<?php

namespace App\Actions;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class APITokens
{
    /**
     * @throws FileNotFoundException
     */
    public function setup(string $path, string $frontend, bool $enabled): void
    {
        if ($enabled) {
            return;
        }

        File::delete($path.'/app/Http/Controllers/TokenController.php');
        File::delete($path.'/app/Http/Resources/TokenResource.php');
        File::delete($path.'/tests/Feature/TokenTest.php');

        $settingsRoutes = File::get($path.'/routes/settings.php');
        $settingsRoutes = remove_blocks($settingsRoutes, 'tokens-routes');
        File::put($path.'/routes/settings.php', $settingsRoutes);

        File::deleteDirectory($path.'/resources/js/pages/tokens');

        $tokensMenuPattern = '/\{\s*title:\s*\'API Tokens\',\s*href:\s*route\([^)]+\),\s*icon:\s*CommandIcon,\s*\},?\n?/m';

        $settingsLayout = match ($frontend) {
            'Vue' => File::get($path.'/resources/js/layouts/settings/layout.vue'),
            'React' => File::get($path.'/resources/js/layouts/settings/layout.tsx'),
            default => throw new \RuntimeException("Unsupported frontend: {$frontend}"),
        };

        $settingsLayout = preg_replace($tokensMenuPattern, '', $settingsLayout);
        File::put($path.'/resources/js/layouts/settings/layout.tsx', $settingsLayout);

        $userModel = File::get($path.'/app/Models/User.php');
        $userModel = str_replace('use HasApiTokens;', '', $userModel);
        $userModel = str_replace('* @property Collection<int, PersonalAccessToken> $tokens', '', $userModel);
        File::put($path.'/app/Models/User.php', $userModel);

        File::delete($path.'/database/migrations/2025_07_10_214649_create_personal_access_tokens_table.php');
        File::delete($path.'/app/Models/PersonalAccessToken.php');
    }

    public function cleanup(string $path, bool $enabled): void
    {
        if ($enabled) {
            return;
        }

        exec('composer remove laravel/sanctum --working-dir='.$path);
    }
}
