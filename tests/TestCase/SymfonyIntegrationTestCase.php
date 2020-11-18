<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\TestCase;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use LegacyTests\Unit\ContextMocker;
use LegacyTests\PrestaShopBundle\Utils\DatabaseCreator as Database;

class SymfonyIntegrationTestCase extends WebTestCase
{
    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Client
     */
    protected $client;

    protected function setUp()
    {
        parent::setUp();
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();

        $this->client = self::createClient();

        //createClient already creates the kernel
        //$this->bootKernel();
        $this->container = self::$kernel->getContainer();

        // Global var for SymfonyContainer
        global $kernel;
        $kernel = self::$kernel;
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->contextMocker->resetContext();
    }

    public static function setUpBeforeClass()
    {
        Database::restoreTestDB();
        require_once __DIR__ . '/../../config/config.inc.php';
    }
}
