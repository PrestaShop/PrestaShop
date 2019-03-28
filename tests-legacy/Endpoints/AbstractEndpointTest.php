<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Endpoints;

use LegacyTests\Unit\ContextMocker;
use PHPUnit\Framework\TestCase;

abstract class AbstractEndpointTest extends TestCase
{

    /**
     * @var ContextMocker
     */
    protected $contextMocker;

    protected function setUp()
    {
        parent::setUp();
        define('_PS_IN_TEST_', true);
        define('_PS_ROOT_DIR_', __DIR__ . '/../..');
        //define('_DB_PREFIX_', 'ps_');
        require_once _PS_ROOT_DIR_ . '/config/defines.inc.php';
        require_once _PS_CONFIG_DIR_ . 'autoload.php';
        require_once _PS_CONFIG_DIR_ . 'bootstrap.php';
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();
    }
}
