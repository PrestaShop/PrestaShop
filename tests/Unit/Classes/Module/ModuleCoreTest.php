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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Module;

use Module;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class FakeModule extends Module
{
}

class ModuleCoreTest extends TestCase
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

    protected function setUp(): void
    {
        if (!defined('_PS_VERSION_')) {
            define('_PS_VERSION_', '1.6.1.0');
        }
    }

    public function testDisplayErrorShouldReturnSimpleError()
    {
        // given
        $error = 'This is an error!';
        $module = new FakeModule();

        // when
        $htmlOutput = $module->displayError($error);

        // then
        $crawler = new Crawler($htmlOutput);
        $this->assertStringContainsString($error, $crawler->filter('.module_error')->text());
    }

    public function testDisplayErrorShouldReturnMultipleErrors()
    {
        // given
        $errors = [
            'Error 1',
            'Error 2',
            'Error 3',
        ];

        $module = new FakeModule();

        // when
        $htmlOutput = $module->displayError($errors);

        // then
        $crawler = new Crawler($htmlOutput);
        $this->assertCount(3, $crawler->filter('.module_error li'));
    }

    /**
     * @return void
     */
    public function testGetDefaultMultistoreCompatibility(): void
    {
        $module = new FakeModule();
        $this->assertEquals(FakeModule::MULTISTORE_COMPATIBILITY_UNKNOWN, $module->getMultistoreCompatibility());
    }
}
