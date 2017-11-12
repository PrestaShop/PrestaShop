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

namespace PrestaShop\PrestaShop\Tests\Unit\Classes\Module;

use DomDocument;
use Module;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DomCrawler\Crawler;

class FakeModule extends Module
{
}

class ModuleCoreTest extends PHPUnit_Framework_TestCase
{
    private $error_string_res = '<div class="bootstrap">
										<div class="module_error alert alert-danger" >
											<button  type="button" class="close" data-dismiss="alert">&times;</button>This is an error!
										</div>
									</div>';

    private $error_array_res = '<div class="bootstrap">
									<div class="module_error alert alert-danger" >
										<button type="button" class="close" data-dismiss="alert">&times;</button>
										<ul><li>Error 1</li><li>Error 2</li><li>Error 3</li></ul>
									</div>
								</div>';

    public function setUp()
    {
        if (!defined('_PS_VERSION_')) {
            define('_PS_VERSION_', '1.6.1.0');
        }
    }

    public function testDisplayError_shouldReturnSimpleError()
    {
        // given
        $error = 'This is an error!';
        $module = new FakeModule();

        // when
        $htmlOutput = $module->displayError($error);

        // then
        $crawler = new Crawler($htmlOutput);
        $this->assertContains($error, $crawler->filter('.module_error')->text());
    }

    public function testDisplayError_shouldReturnMultipleErrors()
    {
        // given
        $errors = array(
            'Error 1',
            'Error 2',
            'Error 3',
        );

        $module = new FakeModule();

        // when
        $htmlOutput = $module->displayError($errors);

        // then
        $crawler = new Crawler($htmlOutput);
        $this->assertCount(3, $crawler->filter('.module_error li'));
    }
}
