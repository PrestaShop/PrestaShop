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


namespace PrestaShop\PrestaShop\Tests\Integration;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

class SmartySettingsTest extends IntegrationTestCase
{
    private $smarty;

    public function setUp()
    {
        parent::setUp();
        global $smarty;
        $this->smarty = $smarty;
        $this->smarty->force_compile = true;
    }

    private function render($templateString, array $parameters)
    {
        $this->smarty->assign($parameters);
        return $this->smarty->fetch('string:' . $templateString);
    }

    public function test_a_link_is_escaped_automatically()
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            '&lt;a&gt;hello&lt;/a&gt;',
            $this->escapeTemplateLocationComments(
                $this->render('{$str}', ['str' => $str])
            )
        );
    }

    public function test_nofilter_prevents_escape()
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            $str,
            $this->escapeTemplateLocationComments(
                $this->render('{$str nofilter}', ['str' => $str])
            )
        );
    }

    public function test_html_is_not_escaped_twice()
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            '&lt;a&gt;hello&lt;/a&gt;',
            $this->escapeTemplateLocationComments(
                $this->render('{$str|escape:"html"}', ['str' => $str])
            )
        );
    }

    private function escapeTemplateLocationComments($string)
    {
        return preg_replace('/\\n<!--(.|\s)*?-->\\n/', '', $string);
    }
}
