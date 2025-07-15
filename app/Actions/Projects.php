<?php

namespace App\Actions;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

class Projects
{
    protected array $filesToDelete = [
        'app/Enums/ProjectRole.php',
        'app/Http/Resources/ProjectResource.php',
        'app/Http/Resources/ProjectUserResource.php',
        'app/Mail/ProjectInvitation.php',
        'app/Models/Project.php',
        'app/Models/ProjectUser.php',
        'app/Policies/ProjectPolicy.php',
        'app/Providers/ProjectServiceProvider.php',
        'app/Traits/HasProjects.php',
        'database/factories/ProjectFactory.php',
        'database/migrations/2025_06_29_115509_create_projects_table.php',
        'database/migrations/2025_06_29_115510_create_project_user_table.php',
        'database/migrations/2025_07_12_212608_add_current_project_id_to_users_table.php',
        'resources/js/components/project-switch.tsx',
        'resources/js/components/project-switch.vue',
        'resources/js/types/project.d.ts',
        'resources/js/types/project-user.d.ts',
        'resources/emails/project-invitation.blade.php',
    ];

    protected array $directoriesToDelete = [
        'app/Actions/Project',
        'app/Http/Controllers/Project',
        'tests/Feature/Project',
        'resources/js/pages/projects',
    ];

    protected array $filesToRemoveBlocks = [
        'bootstrap/providers.php',
        'app/Models/User.php',
        'resources/js/components/nav-main.tsx',
        'resources/js/components/nav-main.vue',
        'resources/js/components/app-header.tsx',
        'resources/js/components/app-header.vue',
        'resources/js/layouts/settings/layout.tsx',
        'resources/js/layouts/settings/layout.vue',
        'routes/settings.php',
    ];

    protected array $filesToRename = [

    ];

    protected array $directoriesToRename = [

    ];

    protected array $filesToReplaceNames = [

    ];

    /**
     * @throws FileNotFoundException
     */
    public function setup(string $path, string $name): void
    {
        if ($name === 'projects') {
            return;
        }

        if ($name === 'none') {
            delete_files($path, $this->filesToDelete);
            delete_directories($path, $this->directoriesToDelete);
            remove_blocks($path, $this->filesToRemoveBlocks, 'projects');

            return;
        }

        throw new \RuntimeException("$name is not supported yet!");
    }

    /**
     * @throws FileNotFoundException
     */
    public function cleanup(string $path): void
    {
        remove_block_tags($path, $this->filesToRemoveBlocks, 'projects');
    }
}
