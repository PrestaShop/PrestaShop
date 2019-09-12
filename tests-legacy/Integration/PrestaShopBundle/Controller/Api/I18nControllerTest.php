<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace LegacyTests\Integration\PrestaShopBundle\Controller\Api;

/**
 * @group api
 * @group i18n
 */
class I18nControllerTest extends ApiTestCase
{
    /**
     * @dataProvider getBadListTranslations
     * @test
     *
     * @param $params
     */
    public function itShouldReturnBadResponseWhenRequestingListOfTranslations($params)
    {
        $this->assertBadRequest('api_i18n_translations_list', $params);
    }

    /**
     * @dataProvider getGoodListTranslations
     * @test
     *
     * @param $params
     */
    public function itShouldReturnOkResponseWhenRequestingListOfTranslations($params)
    {
        $this->assertOkRequest('api_i18n_translations_list', $params);
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
}
