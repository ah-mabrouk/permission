<?php

namespace Mabrouk\RolePermissionGroup\Tests;

use Mabrouk\RolePermissionGroup\RolePermissionGroupServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
  public function setUp(): void
  {
    parent::setUp();
    // additional setup
  }

  protected function getPackageProviders($app)
  {
    return [
        RolePermissionGroupServiceProvider::class,
    ];
  }

  protected function getEnvironmentSetUp($app)
  {
    // perform environment setup
  }
}
