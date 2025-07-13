<?php

namespace App\Actions;

class Billing
{
    public function setup(string $path, bool $enabled): void
    {
        if ($enabled) {
            return;
        }

        $billingRoutes = file_get_contents($path.'/routes/billing.php');
        $billingRoutes = remove_blocks($billingRoutes, 'billing-routes');
        file_put_contents($path.'/routes/billing.php', $billingRoutes);

        $billingLayout = file_get_contents($path.'/resources/js/layouts/billing/layout.vue');
        $billingLayout = preg_replace('/\{\s*title:\s*\'Billing\',\s*href:\s*route\([^)]+\),\s*icon:\s*CreditCardIcon,\s*\},?\n?/m', '', $billingLayout);
        file_put_contents($path.'/resources/js/layouts/billing/layout.vue', $billingLayout);
    }

    public function cleanup(string $path, bool $enabled): void
    {
        if ($enabled) {
            return;
        }

        exec('composer remove laravel/cashier --working-dir='.$path);
        exec('composer remove laravel/cashier-paddle --working-dir='.$path);
    }
}
