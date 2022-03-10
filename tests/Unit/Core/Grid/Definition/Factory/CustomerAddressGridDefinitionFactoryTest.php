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

namespace Tests\Unit\Core\Grid\Definition\Factory;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Grid\Definition\Factory\CustomerAddressGridDefinitionFactory;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\TranslatorInterface;

class CustomerAddressGridDefinitionFactoryTest extends TestCase
{
    public function testItSetsProperBackURLToEditAndDeleteActions()
    {
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);

        $translatorMock = $this->createMock(TranslatorInterface::class);
        $translatorMock->method('trans')->willReturn('Translated String');

        $uri = 'http://localhost/admin-dev/index.php/sell/customers/1/view?_route=0&customer_address%5Boffset%5D=10&customer_address%5Blimit%5D=10&_token=my_super_token';
        $expected_uri = 'http://localhost/admin-dev/index.php/sell/customers/1/view?_route=0&_token=my_super_token';
        $request = Request::create($uri);

        $grid_definition_factory = new CustomerAddressGridDefinitionFactory($hookDispatcherMock, $request);
        $grid_definition_factory->setTranslator($translatorMock);

        $definitions = $grid_definition_factory->getDefinition();
        /** @var \PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection $columns */
        $columns = $definitions->getColumns();
        $array_columns = $columns->move('actions', 0)->toArray();
        $actions = reset($array_columns)['options']['actions'];

        $this->assertEquals(count($actions), 2);

        $edit_action = $actions->current();
        $this->assertEquals($actions->key(), 'edit');
        $this->assertEquals($edit_action->getOptions()['extra_route_params']['back'], $expected_uri);

        $delete_action = $actions->next();
        $this->assertEquals($actions->key(), 'delete');
        $this->assertEquals($delete_action->getOptions()['extra_route_params']['back'], $expected_uri);
    }

    public function testItCreatesWithNullRequest()
    {
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);

        $this->expectNotToPerformAssertions();
        new CustomerAddressGridDefinitionFactory($hookDispatcherMock, null);
    }

    public function testItDoesntChangeGivenHttpRequest()
    {
        $hookDispatcherMock = $this->createMock(HookDispatcherInterface::class);

        $initial_query = '_route=0&customer_address%5Boffset%5D=10&customer_address%5Blimit%5D=10&_token=my_super_token';
        $uri = 'http://localhost/admin-dev/index.php/sell/customers/1/view?' . $initial_query;

        $request = Request::create($uri);
        $this->assertEquals($request->server->get('QUERY_STRING'), $initial_query);
        new CustomerAddressGridDefinitionFactory($hookDispatcherMock, $request);
        $this->assertEquals($request->server->get('QUERY_STRING'), $initial_query);
    }
}
