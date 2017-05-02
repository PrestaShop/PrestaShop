<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Tests\Integration\PrestaShopBundle\Controller\Api;

/**
 * @group api
 * @group supplier
 */
class I18nControllerTest extends ApiTestCase
{
    // Translation list

    /**
     * @dataProvider getBadListTranslations
     * @test
     *
     * @param $params
     */
    public function it_should_return_bad_response_when_requesting_list_of_translations($params)
    {
        $this->assertBadRequest('api_i18n_translations_list', $params);
    }

    /**
     * @dataProvider getGoodListTranslations
     * @test
     *
     * @param $params
     */
    public function it_should_return_ok_response_when_requesting_list_of_translations($params)
    {
        $this->assetOkRequest('api_i18n_translations_list', $params);
    }

    /**
     * @return array
     */
    public function getBadListTranslations()
    {
        return array(
            array(
                array('page' => 'internationnal'), // syntax error wanted
            ),
            array(
                array('page' => 'stockk'), // syntax error wanted
            ),
        );
    }

    /**
     * @return array
     */
    public function getGoodListTranslations()
    {
        return array(
            array(
                array('page' => 'international'),
            ),
            array(
                array('page' => 'stock'),
            ),
        );
    }


    // Domain retrieve
    /**
     * @dataProvider getBadDomains
     * @test
     *
     * @param $params
     */
    public function it_should_return_bad_response_when_requesting_domain($params)
    {
        $this->assertBadRequest('api_i18n_domain_list', $params);
    }

    /**
     * @dataProvider getGoodDomains
     * @test
     *
     * @param $params
     */
    public function it_should_return_ok_response_when_requesting_domain($params)
    {
        $this->assetOkRequest('api_i18n_domain_list', $params);
    }

    /**
     * @return array
     */
    public function getBadDomains()
    {
        return array(
            array(
                array('locale' => 'default', 'domain' => 'AdminGloabl'), // syntax error wanted
            ),
            array(
                array('locale' => 'defaultt', 'domain' => 'AdminGlobal'),
            ),
        );
    }

    /**
     * @return array
     */
    public function getGoodDomains()
    {
        return array(
            array(
                array('locale' => 'default', 'domain' => 'AdminGlobal'),
            ),
            array(
                array('locale' => 'default', 'domain' => 'AdminNavigationMenu'),
            ),
        );
    }


    /**
     * @param $route
     * @param $params
     */
    private function assertBadRequest($route, $params)
    {
        $route = $this->router->generate($route, $params);
        $this->client->request('GET', $route);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $this->client->getResponse();
        $this->assertEquals(400, $response->getStatusCode(), 'It should return a response with "Bad Request" Status.');
    }

    /**
     * @param $route
     * @param $params
     */
    private function assetOkRequest($route, $params)
    {
        $route = $this->router->generate($route, $params);
        $this->client->request('GET', $route);

        /** @var \Symfony\Component\HttpFoundation\Response $response */
        $response = $this->client->getResponse();
        $this->assertEquals(200, $response->getStatusCode(), 'It should return a response with "OK" Status.');
    }
}
