#!/usr/bin/env php
<?php

require_once __DIR__ . '/PrestaShopBundle/Utils/DatabaseCreator.php';

try {
    \LegacyTests\PrestaShopBundle\Utils\DatabaseCreator::createTestDB();
} catch (Throwable $e) {
    echo (string) $e;
    exit(1);
}
