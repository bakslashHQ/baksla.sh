<?php

declare(strict_types=1);

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\ErrorHandler;
use Symfony\Component\Filesystem\Filesystem;

require dirname(__DIR__) . '/vendor/autoload.php';
require dirname(__DIR__) . '/tests/functions.php';

(new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

$filesystem = new Filesystem();
$filesystem->remove([__DIR__ . '/../var/cache/test']);

// Prevent https://github.com/symfony/symfony/issues/53812
set_exception_handler([new ErrorHandler(), 'handleException']);
