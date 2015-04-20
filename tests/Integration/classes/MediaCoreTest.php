<?php
/*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Tests\Integration\Classes;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use Configuration;
use Media;

class MediaCoreTest extends IntegrationTestCase
{
	public function isCssInputsProvider()
	{
		return array(
			array('http://wwww.google.com/images/nav_logo.png', '', 'http://wwww.google.com/images/nav_logo.png', false),
			array('url(http://wwww.google.com/images/nav_logo1.png)', '', 'url(http://wwww.google.com/images/nav_logo1.png)', false),
			array('url("http://wwww.google.com/images/nav_logo2.png")', '', 'url("http://wwww.google.com/images/nav_logo2.png")', false),
			array(' url(\'http://wwww.google.com/images/nav_logo3.png\')', '', 'url(\'http://wwww.google.com/images/nav_logo3.png\')', false),
			array('background: url(http://wwww.google.com/images/nav_logo4.png)', '', 'background:url(http://wwww.google.com/images/nav_logo4.png)', false),
			array('url(https://wwww.google.com/images/nav_logo5.png)', '', 'url(https://wwww.google.com/images/nav_logo5.png)', false),
			array('url(data://wwww.google.com/images/nav_logo6.png)', '', 'url(data://wwww.google.com/images/nav_logo6.png)', false),
			array('url(//wwww.google.com/images/nav_logo7.png)', '', 'url(//wwww.google.com/images/nav_logo7.png)', false),
			array('url("//wwww.google.com/images/nav_logo8.png")', '', 'url("//wwww.google.com/images/nav_logo8.png")', false),
			array('url(\'//wwww.google.com/images/nav_logo9.png\')', '', 'url(\'//wwww.google.com/images/nav_logo9.png\')', false),
			array('background: url(../img/contact-form1.png)', '/themes/default-bootstrap/css/contact-form.css', 'background:url(http://debian/themes/default-bootstrap/css/../img/contact-form1.png)', true),
			array('background: url(./contact-form2.png)', '/themes/default-bootstrap/css/contact-form.css', 'background:url(http://debian/themes/default-bootstrap/css/./contact-form2.png)', true),
			array('background: url(/img/contact-form3.png)', '/themes/default-bootstrap/css/contact-form.css', 'background:url(http://debian/img/contact-form3.png)', true),
			array('background: url(/PrestaShop/img/contact-form4.png)', '/PrestaShop/themes/default-bootstrap/css/contact-form.css', 'background:url(http://debian/PrestaShop/img/contact-form4.png)', true),
		);
	}

	public function testCorrectJQueryNoConflictURL()
	{
		$result = Media::getJqueryPath('1.11');
		$this->assertEquals(true, in_array('http://debian'.__PS_BASE_URI__.'js/jquery/jquery.noConflict.php?version=1.11', $result));
	}

	/**
	 * @dataProvider isCssInputsProvider
	 */
	public function testMinifyCSS($input, $fileuri, $expected)
	{
		$return = Media::minifyCSS($input, $fileuri, $import_url);
		$this->assertEquals($expected, $return, 'MinifyCSS failed for data input : '.$input.'; Expected : '.$expected);
	}

	/**
	 * @dataProvider isCssInputsProvider
	 */
	public function testReplaceByAbsoluteURLPattern($input, $fileuri, $output, $expected)
	{
		$return = preg_match(Media::$pattern_callback, $input, $matches);
		$this->assertEquals((bool)$expected, (bool)$return, 'ReplaceByAbsoluteURLPattern failed for data input : '.$input.(isset($matches[2]) && $matches[2] ? '; Matches : '.$matches[2] : ''));
	}
}
