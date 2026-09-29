<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Las vistas usan @vite(); en los tests no hace falta compilar los assets.
        $this->withoutVite();
    }
}
