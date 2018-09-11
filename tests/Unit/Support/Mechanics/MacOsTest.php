<?php

namespace Tests\Unit\Support\Mechanics;

use App\Support\Console\ServerBag;
use App\Support\Mechanics\MacOs;
use Tests\TestCase;

class MacOsTest extends TestCase
{
    /** @test */
    public function it_returns_the_home_directory()
    {
        $this->app->instance(ServerBag::class, new ServerBag(['HOME'=>'/Users/keoghan']));

        $this->assertEquals('/Users/keoghan', app(MacOs::class)->getUserHomePath());
    }
}
