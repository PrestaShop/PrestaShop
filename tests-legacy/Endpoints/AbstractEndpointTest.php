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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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

    protected function setUp(): void
    {
        define('_PS_ROOT_DIR_', __DIR__ . '/../..');
        define('_PS_ADMIN_DIR_', _PS_ROOT_DIR_ . '/admin-dev');
        require_once _PS_ROOT_DIR_ . '/config/defines.inc.php';
        require_once _PS_CONFIG_DIR_ . 'autoload.php';
        require_once _PS_CONFIG_DIR_ . 'bootstrap.php';
        $this->contextMocker = new ContextMocker();
        $this->contextMocker->mockContext();
        parent::setUp();
    }
}
