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

namespace Tests\Integration\Controllers;

use AdminSearchControllerCore;
use Context;
use Employee;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AdminSearchControllerCoreTest extends KernelTestCase
{
    /**
     * @dataProvider dataProviderSearch
     *
     * @param string $query
     * @param array $result
     *
     * @return void
     */
    public function testSearch(string $query, array $result): void
    {
        $_POST['bo_search_type'] = '';
        $_POST['bo_query'] = $query;
        Context::getContext()->employee = new Employee(1);

        $controller = new AdminSearchControllerCore();
        $controller->postProcess();
        $controller->renderView();

        $templateVars = $controller->getTemplateViewVars();

        self::assertEquals($this->cleanDataToken($templateVars), $this->cleanDataToken($result));
    }

    public function dataProviderSearch(): array
    {
        return [
            [
                '',
                [
                    'query' => '',
                    'show_toolbar' => true,
                    'nb_results' => 0,
                    'searchPanels' => [
                        [
                            'title' => 'Search docs.prestashop-project.org',
                            'button_label' => 'Go to the documentation',
                            'link' => 'https://docs.prestashop-project.org/welcome/?q=',
                            'same_page' => false,
                        ],
                    ],
                ],
            ],
            [
                'orders',
                [
                    'query' => 'orders',
                    'show_toolbar' => true,
                    'nb_results' => 1,
                    'features' => [
                        'Orders' => [
                            [
                                'link' => 'http://localhost/admin-dev/index.php?controller=AdminOrders&bo_query=orders',
                            ],
                        ],
                    ],
                    'searchPanels' => [
                        [
                            'title' => 'Search docs.prestashop-project.org',
                            'button_label' => 'Go to the documentation',
                            'link' => 'https://docs.prestashop-project.org/welcome/?q=orders',
                            'same_page' => false,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function cleanDataToken(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $data[$key] = preg_replace('#&token=[a-z0-9]+#', '', $value, 1);
            }
            if (is_array($value)) {
                $data[$key] = $this->cleanDataToken($value);
            }
        }

        return $data;
    }
}
