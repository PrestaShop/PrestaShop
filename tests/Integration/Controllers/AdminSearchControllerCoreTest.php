<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
                            'is_external_link' => true,
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
                            'is_external_link' => true,
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
