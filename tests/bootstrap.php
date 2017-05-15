<?php

if (PHP_VERSION_ID < 50600) {
    ini_set('mbstring.internal_encoding', 'UTF-8');
}

require_once __DIR__ . '/../vendor/autoload.php';
