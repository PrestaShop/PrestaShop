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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Smarty;

use PHPUnit\Framework\TestCase;

class SmartySettingsTest extends TestCase
{
    private $smarty;

    protected function setUp(): void
    {
        parent::setUp();

        global $smarty;
        $this->smarty = $smarty;
        $this->smarty->force_compile = true;
        $this->smarty->escape_html = true;
    }

    private function render(string $templateString, array $parameters): string
    {
        return $this->smarty
            ->assign($parameters)
            ->fetch('string:' . $templateString);
    }

    private function escapeTemplateLocationComments(string $string): string
    {
        return preg_replace('/\\n<!--(.|\s)*?-->\\n/', '', $string);
    }

    public function testALinkIsEscapedAutomatically(): void
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            '&lt;a&gt;hello&lt;/a&gt;',
            $this->escapeTemplateLocationComments(
                $this->render('{$str}', ['str' => $str])
            )
        );
    }

    public function testNofilterPreventsEscape(): void
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            $str,
            $this->escapeTemplateLocationComments(
                $this->render('{$str nofilter}', ['str' => $str])
            )
        );
    }

    public function testHtmlIsNotEscapedTwice(): void
    {
        $str = '<a>hello</a>';
        $this->assertEquals(
            '&lt;a&gt;hello&lt;/a&gt;',
            $this->escapeTemplateLocationComments(
                $this->render('{$str|escape:"html"}', ['str' => $str])
            )
        );
    }
}
