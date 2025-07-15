<?php

namespace App\Actions;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

class Billing
{
    protected array $filesToDelete = [
        'config/billing.php',
        'app/DTOs/BillingPlanDTO.php',
        'app/Models/Subscription.php',
        'app/Providers/BillingServiceProvider.php',
        'resources/views/layouts/billing.blade.php',
        'database/migrations/2019_05_03_000001_create_customers_table.php',
        'database/migrations/2019_05_03_000002_create_subscriptions_table.php',
        'database/migrations/2019_05_03_000003_create_subscription_items_table.php',
        'database/migrations/2019_05_03_000004_create_transactions_table.php',
        'resources/js/types/plan.d.ts',
        'resources/js/types/subscription.d.ts',
    ];

    protected array $directoriesToDelete = [
        'app/Http/Controllers/Billing',
        'resources/views/billing',
        'tests/Feature/Billing',
    ];

    protected array $filesToRemoveBlocks = [
        'routes/web.php',
        'bootstrap/providers.php',
        'resources/js/components/app-sidebar.tsx',
        'resources/js/components/app-sidebar.vue',
        'resources/js/components/user-menu-content.tsx',
        'resources/js/components/user-menu-content.vue',
        'resources/views/index.blade.php',
        'resources/views/components/navbar.blade.php',
        'app/Models/User.php',
        '.env',
        '.env.example',
    ];

    /**
     * @throws FileNotFoundException
     */
    public function setup(string $path, string $billing): void
    {
        if ($billing === 'stripe') {
            throw new \RuntimeException('Stripe is not supported yet.');
        }

        if ($billing !== 'none') {
            return;
        }

        delete_files($path, $this->filesToDelete);
        delete_directories($path, $this->directoriesToDelete);
        remove_blocks($path, $this->filesToRemoveBlocks, 'billing');

        // manual removals
        $userModel = File::get($path.'/app/Models/User.php');
        $userModel = str_replace('@method Subscription|null subscription($type = \'default\')', '', $userModel);
        File::put($path.'/app/Models/User.php', $userModel);
    }

    /**
     * @throws FileNotFoundException
     */
    public function cleanup(string $path, string $billing): void
    {
        remove_block_tags($path, $this->filesToRemoveBlocks, 'billing');

        if ($billing === 'none') {
            exec('composer remove laravel/cashier --working-dir='.$path);
            exec('composer remove laravel/cashier-paddle --working-dir='.$path);

            return;
        }

        if ($billing === 'stripe') {
            exec('composer remove laravel/cashier-paddle --working-dir='.$path);
        }

        if ($billing === 'paddle') {
            exec('composer remove laravel/cashier --working-dir='.$path);
        }
    }
}
