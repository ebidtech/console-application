<?php

/*
 * This file is a part of the EBDate library.
 *
 * (c) 2014 Ebidtech
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Let the commands live.
set_time_limit(0);

// Define external variables.
$appName = null;
$appVersion = null;

// Require the auto load file.
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/application.php';

// Create the container.
$container = new \ConsoleApplication\DependencyInjection\Container(dirname(__DIR__));
