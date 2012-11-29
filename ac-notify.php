#!/usr/bin/env php
<?php

/*
 * This file is part of the ActiveCollab Notify utility.
 */

/**
 * @author Kosta Harlan <kostajh@gmail.com>
 */

// installed via composer?
if (file_exists($a = __DIR__.'/../../autoload.php')) {
    require_once $a;
} else {
    require_once __DIR__.'/vendor/autoload.php';
}

use ActiveCollabNotify\Console\Application;

$application = new Application();
$application->run();
