<?php

namespace Electrik\Tests\Feature;

use Electrik\Tests\TestCase;

class ConfigTest extends TestCase
{
    public function test_electrik_config_is_merged(): void
    {
        $this->assertSame('5.0.0-alpha.14', config('electrik.version'));
        $this->assertTrue(config('electrik.onboarding.enabled'));
        $this->assertContains('onboarding', config('electrik.onboarding.exempt_routes'));
    }

    public function test_install_and_seed_demo_commands_are_registered(): void
    {
        $kernel = $this->app->make(\Illuminate\Contracts\Console\Kernel::class);
        $commands = array_keys($kernel->all());

        $this->assertContains('electrik:install', $commands);
        $this->assertContains('electrik:seed-demo', $commands);
    }
}
