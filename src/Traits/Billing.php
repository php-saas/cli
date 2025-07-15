<?php

namespace PHPSaaS\Cli\Traits;

use Illuminate\Contracts\Filesystem\FileNotFoundException;

trait Billing
{
    protected array $billingFilesToDelete = [
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

    protected array $billingDirectoriesToDelete = [
        'app/Http/Controllers/Billing',
        'resources/views/billing',
        'tests/Feature/Billing',
    ];

    protected array $billingFilesToRemoveBlocks = [
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
    protected function setupBilling(): void
    {
        if ($this->billing === 'stripe') {
            throw new \RuntimeException('Stripe is not supported yet.');
        }

        if ($this->billing !== 'none') {
            return;
        }

        foreach ($this->billingFilesToDelete as $file) {
            $this->fileSystem->delete($this->path.'/'.$file);
        }
        foreach ($this->billingDirectoriesToDelete as $directory) {
            if (! $this->fileSystem->isDirectory($directory)) {
                continue;
            }
            $this->fileSystem->deleteDirectory($this->path.'/'.$directory);
        }
        $this->removeBlocks($this->billingFilesToRemoveBlocks, 'billing');

        // manual removals
        $userModel = $this->fileSystem->get($this->path.'/app/Models/User.php');
        $userModel = str_replace('@method Subscription|null subscription($type = \'default\')', '', $userModel);
        $this->fileSystem->put($this->path.'/app/Models/User.php', $userModel);
    }

    protected function cleanupBilling(): void
    {
        $this->removeBlockTags($this->billingFilesToRemoveBlocks, 'billing');

        if ($this->billing === 'none') {
            $this->runCommands([
                composer_binary().' remove laravel/cashier --working-dir='.$this->path,
                composer_binary().' remove laravel/cashier-paddle --working-dir='.$this->path,
            ]);

            return;
        }

        if ($this->billing === 'stripe') {
            $this->runCommands([
                composer_binary().' remove laravel/cashier-paddle --working-dir='.$this->path,
            ]);
        }

        if ($this->billing === 'paddle') {
            $this->runCommands([
                composer_binary().' remove laravel/cashier --working-dir='.$this->path,
            ]);
        }
    }
}
