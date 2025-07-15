<?php

namespace App\Commands;

use App\Actions\APITokens;
use App\Actions\Application;
use App\Actions\Billing;
use App\Actions\Frontend;
use App\Actions\Npm;
use App\Actions\Projects;
use App\Actions\Tests;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
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

    protected string $npm = '';

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $this->getOutput()->write('<fg=bright-magenta>
 ▗▄▄▖ ▗▖ ▗▖▗▄▄▖     ▗▄▄▖ ▗▄▖  ▗▄▖  ▗▄▄▖
 ▐▌ ▐▌▐▌ ▐▌▐▌ ▐▌   ▐▌   ▐▌ ▐▌▐▌ ▐▌▐▌
 ▐▛▀▘ ▐▛▀▜▌▐▛▀▘     ▝▀▚▖▐▛▀▜▌▐▛▀▜▌ ▝▀▚▖
 ▐▌   ▐▌ ▐▌▐▌      ▗▄▄▞▘▐▌ ▐▌▐▌ ▐▌▗▄▄▞▘
        </>'.PHP_EOL);

        $name = $this->argument('name');

        $this->path = getcwd().'/'.$name;

        $this->collectInputs();

        $this->setup();

        $this->cleanup();

        $this->info("Application '{$name}' created successfully.");
    }

    /**
     * @throws FileNotFoundException
     */
    private function setup(): void
    {
        app(Application::class)->setup($this->path, $this->argument('name'));
        app(Frontend::class)->setup($this->path, $this->frontend);
        app(Tests::class)->setup($this->path, $this->tests);
        app(APITokens::class)->setup($this->path, $this->apiTokens === 'yes');
        app(Billing::class)->setup($this->path, $this->billing);
        app(Projects::class)->setup($this->path, $this->projects);
        app(Npm::class)->setup($this->path, $this->npm === 'yes');
    }

    private function collectInputs(): void
    {
        $this->frontend = select('Which frontend stack would you like to use?', [
            'react' => 'React',
            'vue' => 'Vue (coming soon)',
        ], hint: 'The frontend stacks are integrated with Inertia.js');

        $this->tests = select('Which testing framework would you like to use?', [
            'phpunit' => 'PHPUnit',
            'pest' => 'Pest (coming soon)',
        ]);

        $this->projects = select('Do you want Projects, Organizations or Teams?', [
            'projects' => 'Projects',
            'organizations' => 'Organizations (coming soon)',
            'teams' => 'Teams (coming soon)',
            'none' => 'None',
        ], default: 'projects');

        $this->billing = select('Which payment provider do you want for Billing?', [
            'paddle' => 'Cashier Paddle',
            'stripe' => 'Cashier Stripe (coming soon)',
            'none' => 'None',
        ]);

        $this->apiTokens = select('Do you want to include API tokens?', [
            'yes' => 'Yes',
            'no' => 'No',
        ], default: 'yes');

        $this->npm = select('Do you want to run npm install?', [
            'yes' => 'Yes',
            'no' => 'No',
        ], default: 'yes');
    }

    /**
     * @throws FileNotFoundException
     */
    private function cleanup(): void
    {
        app(Application::class)->cleanup($this->path);
        app(Frontend::class)->cleanup($this->path);
        app(Tests::class)->cleanup($this->path, $this->tests);
        app(APITokens::class)->cleanup($this->path, $this->apiTokens === 'yes');
        app(Billing::class)->cleanup($this->path, $this->billing);
        app(Projects::class)->cleanup($this->path);

        File::delete($this->path.'/use.sh');

        exec("cd $this->path && php artisan key:generate");
        exec("cd $this->path && php artisan migrate --force");
        exec($this->path.'/vendor/bin/pint --parallel');
    }
}
