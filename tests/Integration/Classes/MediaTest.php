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

namespace Tests\Integration\Classes;

use Configuration;
use Media;
use PHPUnit\Framework\TestCase;

class MediaTest extends TestCase
{
    public function providerCSSInputs(): iterable
    {
        yield ['http://wwww.google.com/images/nav_logo.png', '', 'http://wwww.google.com/images/nav_logo.png', false];
        yield ['url(http://wwww.google.com/images/nav_logo1.png)', '', 'url(http://wwww.google.com/images/nav_logo1.png)', false];
        yield ['url("http://wwww.google.com/images/nav_logo2.png")', '', 'url("http://wwww.google.com/images/nav_logo2.png")', false];
        yield [' url(\'http://wwww.google.com/images/nav_logo3.png\')', '', 'url(\'http://wwww.google.com/images/nav_logo3.png\')', false];
        yield ['background: url(http://wwww.google.com/images/nav_logo4.png)', '', 'background:url(http://wwww.google.com/images/nav_logo4.png)', false];
        yield ['url(https://wwww.google.com/images/nav_logo5.png)', '', 'url(https://wwww.google.com/images/nav_logo5.png)', false];
        yield ['url(data://wwww.google.com/images/nav_logo6.png)', '', 'url(data://wwww.google.com/images/nav_logo6.png)', false];
        yield ['url(\'https://wwww.google.com/images/nav_logo7.png\')', '', 'url(\'https://wwww.google.com/images/nav_logo7.png\')', false];
        yield ['url(\'data://wwww.google.com/images/nav_logo8.png\')', '', 'url(\'data://wwww.google.com/images/nav_logo8.png\')', false];
        yield ['url("https://wwww.google.com/images/nav_logo9.png")', '', 'url("https://wwww.google.com/images/nav_logo9.png")', false];
        yield ['url("data://wwww.google.com/images/nav_logo10.png")', '', 'url("data://wwww.google.com/images/nav_logo10.png")', false];
        yield ['url(//wwww.google.com/images/nav_logo11.png)', '', 'url(//wwww.google.com/images/nav_logo11.png)', false];
        yield ['url("//wwww.google.com/images/nav_logo12.png")', '', 'url("//wwww.google.com/images/nav_logo12.png")', false];
        yield ['url(\'//wwww.google.com/images/nav_logo13.png\')', '', 'url(\'//wwww.google.com/images/nav_logo13.png\')', false];
        yield ['url(http://cdn.server/uri/img/contact-form.png)', '/path/', 'url(http://cdn.server/uri/img/contact-form.png)', false];
        yield [' url(../img/contact-form1.png)', '/themes/classic/css/contact-form.css', 'url(http://server/themes/classic/css/../img/contact-form1.png)', true];
        yield [' url(./contact-form2.png)', '/themes/classic/css/contact-form.css', 'url(http://server/themes/classic/css/./contact-form2.png)', true];
        yield ['url(/img/contact-form3.png)', '/themes/classic/css/contact-form.css', 'url(http://server/img/contact-form3.png)', true];
        yield ['url(\'../img/contact-form4.png\')', '/themes/classic/css/contact-form.css', 'url(\'http://server/themes/classic/css/../img/contact-form4.png\')', true];
        yield [' url(\'./contact-form5.png\')', '/themes/classic/css/contact-form.css', 'url(\'http://server/themes/classic/css/./contact-form5.png\')', true];
        yield ['url(\'/img/contact-form6.png\')', '/themes/classic/css/contact-form.css', 'url(\'http://server/img/contact-form6.png\')', true];
        yield ['url("../img/contact-form7.png")', '/themes/classic/css/contact-form.css', 'url("http://server/themes/classic/css/../img/contact-form7.png")', true];
        yield ['url("./contact-form8.png")', '/themes/classic/css/contact-form.css', 'url("http://server/themes/classic/css/./contact-form8.png")', true];
        yield ['url("/img/contact-form9.png")', '/themes/classic/css/contact-form.css', 'url("http://server/img/contact-form9.png")', true];
    }

    /**
     * @dataProvider providerCSSInputs
     */
    public function testMinifyCSS(string $input, string $fileuri, string $output): void
    {
        $domain = Configuration::get('PS_SHOP_DOMAIN');
        $output = str_replace('//server/', '//' . $domain . '/', $output);
        $return = Media::minifyCSS($input, $fileuri, $import_url);
        $this->assertEquals($output, $return, 'MinifyCSS failed for data input : ' . $input . '; Expected : ' . $output . '; Returns : ' . $return);
    }

    /**
     * @dataProvider providerCSSInputs
     */
    public function testReplaceByAbsoluteURLPattern(string $input, string $fileuri, string $output, bool $expected): void
    {
        $return = preg_match(Media::$pattern_callback, $input, $matches);
        $this->assertEquals(
            $expected,
            (bool) $return,
            'ReplaceByAbsoluteURLPattern failed for data input : ' . $input . (!empty($matches[2]) ? '; Matches : ' . $matches[2] : '')
        );
    }
}
