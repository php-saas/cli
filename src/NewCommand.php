<?php

namespace PHPSaaS\Cli;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use PHPSaaS\Cli\Traits\Application;
use PHPSaaS\Cli\Traits\Billing;
use PHPSaaS\Cli\Traits\Frontend;
use PHPSaaS\Cli\Traits\InteractWithBlocks;
use PHPSaaS\Cli\Traits\Npm;
use PHPSaaS\Cli\Traits\Projects;
use PHPSaaS\Cli\Traits\RunCommands;
use PHPSaaS\Cli\Traits\Tests;
use PHPSaaS\Cli\Traits\Tokens;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\select;

class NewCommand extends Command
{
    use Application;
    use Billing;
    use Frontend;
    use InteractWithBlocks;
    use Npm;
    use Projects;
    use RunCommands;
    use Tests;
    use Tokens;

    protected string $name = '';

    protected string $path = '';

    protected string $frontend = '';

    protected string $tests = '';

    protected string $projects = '';

    protected string $billing = '';

    protected string $tokens = '';

    protected string $npm = '';

    protected OutputInterface $output;

    protected InputInterface $input;

    protected Filesystem $fileSystem;

    protected function configure(): void
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Laravel application')
            ->addArgument('name', InputArgument::REQUIRED);
    }

    /**
     * @throws FileNotFoundException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->fileSystem = new Filesystem;

        $output->write('<fg=bright-magenta>
 ▗▄▄▖ ▗▖ ▗▖▗▄▄▖     ▗▄▄▖ ▗▄▖  ▗▄▖  ▗▄▄▖
 ▐▌ ▐▌▐▌ ▐▌▐▌ ▐▌   ▐▌   ▐▌ ▐▌▐▌ ▐▌▐▌
 ▐▛▀▘ ▐▛▀▜▌▐▛▀▘     ▝▀▚▖▐▛▀▜▌▐▛▀▜▌ ▝▀▚▖
 ▐▌   ▐▌ ▐▌▐▌      ▗▄▄▞▘▐▌ ▐▌▐▌ ▐▌▗▄▄▞▘
        </>'.PHP_EOL);

        $this->name = $input->getArgument('name');

        $this->path = getcwd().'/'.$this->name;

        $this->collectInputs();

        // setup modules
        $this->setupApplication();
        $this->setupFrontend();
        $this->setupProjects();
        $this->setupTokens();
        $this->setupBilling();
        $this->setupTests();

        // cleanup modules
        $this->cleanupApplication();
        $this->cleanupFrontend();
        $this->cleanupProjects();
        $this->cleanupTokens();
        $this->cleanupBilling();
        $this->cleanupTests();

        // cleanup setup
        $this->fileSystem->delete($this->path.'/use.sh');
        $this->runCommands([
            php_binary()." {$this->path}/artisan key:generate",
            php_binary()." {$this->path}/artisan migrate --force",
            php_binary()." {$this->path}/artisan db:seed --force",
            "{$this->path}/vendor/bin/pint --parallel",
        ]);

        $this->setupNpm();

        return 0;
    }

    protected function collectInputs(): void
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

        $this->tokens = select('Do you want to include API tokens?', [
            'yes' => 'Yes',
            'no' => 'No',
        ], default: 'yes');

        $this->npm = select('Do you want to run npm install?', [
            'yes' => 'Yes',
            'no' => 'No',
        ], default: 'yes');
    }
}
