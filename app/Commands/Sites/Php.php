<?php

namespace App\Commands\Sites;

use App\PhpVersion;
use App\Site;
use LaravelZero\Framework\Commands\Command;

class Php extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'sites:php {site?}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Set the php version for a site.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $name = $this->argument('site') ?: site_from_cwd();

        if (! $name) {
            throw new \Exception("Site '{$name}' not found.");
        }

        $site = Site::firstOrCreateForName($name);

        $option = $this->menu(
            'Available PHP versions',
            PhpVersion::pluck('version_number', 'id')->toArray()
        )->open();

        if (! $option) {
            return;
        }

        $site->setPhpVersion($option);
    }
}
