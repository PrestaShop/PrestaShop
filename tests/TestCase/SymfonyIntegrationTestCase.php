<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\TestCase;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

class SymfonyIntegrationTestCase extends WebTestCase
{
    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    /**
     * @var Client
     */
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();

        $this->client = self::createClient();

        //createClient already creates the kernel
        //$this->bootKernel();
        self::$container = self::$kernel->getContainer();

        // Global var for SymfonyContainer
        global $kernel;
        $kernel = self::$kernel;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->contextMocker->resetContext();
    }

    public static function setUpBeforeClass(): void
    {
        self::restoreTestDB();
        require_once __DIR__ . '/../../config/config.inc.php';
    }

    private static function restoreTestDB(): void
    {
        DatabaseDump::restoreAllTables();
    }
}
