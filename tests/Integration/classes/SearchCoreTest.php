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

namespace Tests\Unit\Classes;

use Tests\TestCase\IntegrationTestCase;
use Configuration;
use Search;

class SearchCoreTest extends IntegrationTestCase
{
    /**
     * @dataProvider keywordsProvider
     */
    public function testGenerationOfSearchKeywordsFromWord($word, $expectedKeyWord, $withStart, $withEnd)
    {
        Configuration::set('PS_SEARCH_START', $withStart);
        Configuration::set('PS_SEARCH_END', !$withEnd); // Opposite of the meaning of start equivalent :)
        $return = Search::getSearchParamFromWord($word);
        $this->assertEquals($expectedKeyWord, $return, 'Search::getSearchParamFromWord() failed for data input : '.$word.'; Expected : '.$expectedKeyWord.'; Returns : '.$return);
    }

    public function keywordsProvider()
    {
        return array(
            array('dress', 'dress%', false, true),
            array('dres', 'dres%', false, true),
            array('dress', '%dress%', true, true),
            array('dress', 'dress', false, false),
            array('dre%ss', 'dre\\\\%ss', false, false),
            array('dre%ss', '%dre\\\\%ss', true, false),
            array('-dress', 'dress%', false, true),
        );
    }
}
