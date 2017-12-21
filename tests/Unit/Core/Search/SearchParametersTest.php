<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Search;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use PrestaShop\PrestaShop\Core\Search\SearchParameters;


class SearchParametersTest extends TestCase
{
    /**
     * @var SearchParameters
     */
    private $searchParameters;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->searchParameters = new SearchParameters();
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        $this->searchParameters = null;
    }

    /**
     * @dataProvider getScenarios
     */
    public function testGetFiltersFromRequest($request, $defaultValues, $expected)
    {
        $this->assertSame($expected, $this->searchParameters->getFiltersFromRequest($request, $defaultValues));
    }

    public function getScenarios()
    {
        return array(
            'IDEAL CASE' => array(
                $this->createRequestParams(
                    array(
                        'limit' => 10,
                        'offset' => 10,
                        'orderBy' => 'foo',
                        'sortOrder' => 'ASC',
                        'filters' => array(
                            'firstname' => 'bar',
                            'lastname' => 'baz',
                        )
                    )
                ),
                array(),
                array(
                    'limit' => 10,
                    'offset' => 10,
                    'orderBy' => 'foo',
                    'sortOrder' => 'ASC',
                    'filters' => array(
                        'firstname' => 'bar',
                        'lastname' => 'baz',
                    )
                )
            ),
            'MISSING BUT DEFAULT CASE' => array(
                $this->createRequestParams(
                    array(
                        'limit' => 10,
                        'offset' => 10,
                        'orderBy' => 'foo',
                        'sortOrder' => 'ASC',
                        'filters' => array(
                            'firstname' => null,
                            'lastname' => 'baz',
                        )
                    )
                ),
                array(
                    'limit' => 10,
                    'offset' => 10,
                    'orderBy' => 'foo',
                    'sortOrder' => 'ASC',
                    'filters' => array(
                        'firstname' => 'bar',
                        'lastname' => 'baz',
                    )
                ),
                array(
                    'limit' => 10,
                    'offset' => 10,
                    'orderBy' => 'foo',
                    'sortOrder' => 'ASC',
                    'filters' => array(
                        'firstname' => 'bar',
                        'lastname' => 'baz',
                    )
                )
            ),
            'MISSING BUT NOT DEFAULT CASE' => array(
                $this->createRequestParams(
                    array(
                        'limit' => 10,
                        'offset' => 10,
                        'orderBy' => 'foo',
                        'sortOrder' => 'ASC',
                        'filters' => array(
                            'firstname' => null,
                            'lastname' => 'baz',
                        ), false
                    )
                ),
                array(
                    'limit' => 10,
                    'offset' => 10,
                    'orderBy' => 'foo',
                    'sortOrder' => 'ASC',
                    'filters' => array(
                        'lastname' => 'baz',
                    )
                ),
                array(
                    'limit' => 10,
                    'offset' => 10,
                    'orderBy' => 'foo',
                    'sortOrder' => 'ASC',
                    'filters' => array(
                        'lastname' => 'baz',
                    )
                )
            ),
        );
    }

    /**
     * @param array $params
     * @param bool $isGET
     *
     * @return Request a complete request.
     */
    private function createRequestParams(array $params, $isGET = true)
    {
        return Request::create('localhost', $isGET ? 'GET' : 'POST', $params);
    }
}