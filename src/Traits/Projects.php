<?php

namespace PHPSaaS\Cli\Traits;

use RuntimeException;

trait Projects
{
    protected array $projectsFilesToDelete = [
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

    protected array $projectsDirectoriesToDelete = [
        'app/Actions/Project',
        'app/Http/Controllers/Project',
        'tests/Feature/Project',
        'resources/js/pages/projects',
    ];

    protected array $projectsFilesToRemoveBlocks = [
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

    protected function setupProjects(): void
    {
        if ($this->projects === 'projects') {
            return;
        }

        if ($this->projects === 'none') {
            foreach ($this->projectsFilesToDelete as $file) {
                $this->fileSystem->delete($this->path.'/'.$file);
            }
            foreach ($this->projectsDirectoriesToDelete as $directory) {
                if (! $this->fileSystem->isDirectory($directory)) {
                    continue;
                }
                $this->fileSystem->deleteDirectory($this->path.'/'.$directory);
            }
            $this->removeBlocks($this->projectsFilesToRemoveBlocks, 'projects');

            return;
        }

        throw new RuntimeException("$this->projects is not supported yet!");
    }

    protected function cleanupProjects(): void
    {
        $this->removeBlockTags($this->projectsFilesToRemoveBlocks, 'projects');
    }
}
