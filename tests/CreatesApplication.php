<?php

declare(strict_types=1);

/**
 * Trait CreatesApplication
 *
 * This trait provides a method to create the application instance
 * used by the testing environment in a Laravel application.
 *
 * Responsibilities:
 * - Loads the application bootstrap script.
 * - Boots up the kernel for the application.
 *
 * @method Application createApplication()
 */

namespace Tests;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        // Load the application bootstrap script.
        $app = require __DIR__ . '/../bootstrap/app.php';

        // Boot up the kernel for the application.
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
