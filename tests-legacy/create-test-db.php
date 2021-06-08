#!/usr/bin/env php
<?php

use LegacyTests\PrestaShopBundle\Utils\DatabaseCreator;

require_once __DIR__ . '/PrestaShopBundle/Utils/DatabaseCreator.php';

try {
    DatabaseCreator::createTestDB();
    echo '-- Create test DB successful! --' . "\n";
} catch (Throwable $e) {
    echo (string) $e;
    exit(1);
}
