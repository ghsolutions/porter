<?php

namespace App\Commands\DockerCompose;

use App\Porter;
use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Start extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'start';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Create and start the containers';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        app(Porter::class)->start();
    }
}
