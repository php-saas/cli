<?php

namespace App\Commands;

use App\Actions\APITokens;
use App\Actions\Application;
use App\Actions\Frontend;
use App\Actions\Tests;
use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Facades\File;

use function Laravel\Prompts\select;
use function Termwind\render;

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

    /**
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        render(<<<'HTML'
         ▗▄▄▖ ▗▖ ▗▖▗▄▄▖     ▗▄▄▖ ▗▄▖  ▗▄▖  ▗▄▄▖
         ▐▌ ▐▌▐▌ ▐▌▐▌ ▐▌   ▐▌   ▐▌ ▐▌▐▌ ▐▌▐▌
         ▐▛▀▘ ▐▛▀▜▌▐▛▀▘     ▝▀▚▖▐▛▀▜▌▐▛▀▜▌ ▝▀▚▖
         ▐▌   ▐▌ ▐▌▐▌      ▗▄▄▞▘▐▌ ▐▌▐▌ ▐▌▗▄▄▞▘
        HTML
        );

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
        app(APITokens::class)->setup($this->path, $this->frontend, $this->apiTokens === 'Yes');
    }

    private function collectInputs(): void
    {
        $this->frontend = 'React';
        $this->tests = 'PHPUnit';
        $this->projects = 'Projects';
        $this->billing = 'Cashier Paddle';
        $this->apiTokens = 'No';

        return;
        $this->frontend = select('Which frontend stack would you like to use?', [
            'React',
            'Vue',
        ]);
        $this->tests = select('Which testing framework would you like to use?', [
            'PHPUnit',
            'Pest',
        ]);
        $this->projects = select('Do you want Projects, Organizations or Teams?', [
            'Projects',
            'Organizations',
            'Teams',
            'None',
        ]);
        $this->billing = select('Which payment provider do you want for Billing?', [
            'Cashier Paddle',
            'Cashier Stripe',
            'None',
        ]);
        $this->apiTokens = select('Do you want to include API tokens?', [
            'Yes',
            'No',
        ]);
    }

    private function cleanup(): void
    {
        app(Application::class)->cleanup($this->path);
        app(Frontend::class)->cleanup($this->path);
        app(Tests::class)->cleanup($this->path, $this->tests);
        app(APITokens::class)->cleanup($this->path, $this->apiTokens === 'Yes');

        File::delete($this->path.'/use.sh');

        exec($this->path.'/vendor/bin/pint --parallel');
    }
}
