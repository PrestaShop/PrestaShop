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

declare(strict_types=1);

namespace Tests\Unit\Core\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Search\ControllerAction;

class ControllerActionTest extends TestCase
{
    /**
     * @dataProvider getControllers
     *
     * @param string $fqcn
     * @param array $result
     */
    public function testGetFromString($fqcn, $result)
    {
        $this->assertEquals($result, ControllerAction::fromString($fqcn));
    }

    /**
     * @return array the list of controller names and expected results
     */
    public function getControllers()
    {
        return [
            ['MyNamespace\Foo\Bar\BarController::fooAction', ['bar', 'foo']],
            ['ModuleNameSpace\YoloController::yoloAction', ['yolo', 'yolo']],
            ['PrestaShop\Controller\Admin\ProductController::formAction', ['product', 'form']],
            ['ModuleController', ['module', 'N/A']],
            ['foo::actionAction', ['N/A', 'action']],
            ['This is not even a FQCN', ['N/A', 'N/A']],
        ];
    }
}
