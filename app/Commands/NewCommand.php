<?php

namespace App\Commands;

use App\Actions\Composer;
use App\Actions\Frontend;
use App\Actions\Git;
use App\Actions\Tests;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use function Laravel\Prompts\select;

class NewCommand extends Command
{
    protected $signature = 'new {name}';

    protected $description = 'Create a new application';

    protected string $path = '';

    protected string $frontend = '';

    protected string $tests = '';

    protected string $projects = '';

    protected string $billing = '';

    protected string $apiTokens = '';

    public function handle(): void
    {
        $name = $this->argument('name');

        $this->path = getcwd() . '/' . $name;

        $this->collectInputs();

        app(Git::class)->clone($name);
        app(Frontend::class)->setup($this->path, $this->frontend);
        app(Tests::class)->setup($this->path, $this->tests);
        app(Composer::class)->setup($this->path);

        $this->cleanup();

        $this->info("Application '{$name}' created successfully.");
    }

    private function collectInputs(): void
    {
        $this->frontend = 'React';
        $this->tests = 'Pest';
        $this->projects = 'Projects';
        $this->billing = 'Cashier Paddle';
        $this->apiTokens = 'Yes';

        return;
        $this->frontend = select("Which frontend stack would you like to use?", [
            'React',
            'Vue',
        ]);
        $this->tests = select("Which testing framework would you like to use?", [
            'PHPUnit',
            'Pest',
        ]);
        $this->projects = select("Do you want Projects, Organizations or Teams?", [
            'Projects',
            'Organizations',
            'Teams',
            'None',
        ]);
        $this->billing = select("Which payment provider do you want for Billing?", [
            'Cashier Paddle',
            'Cashier Stripe',
            'None',
        ]);
        $this->apiTokens = select("Do you want to include API tokens?", [
            'Yes',
            'No',
        ]);
    }

    private function cleanup(): void
    {
        app(Git::class)->cleanup($this->path);
        app(Frontend::class)->cleanup($this->path);
        app(Tests::class)->cleanup($this->path, $this->tests);

        File::delete($this->path . '/use.sh');
    }
}
