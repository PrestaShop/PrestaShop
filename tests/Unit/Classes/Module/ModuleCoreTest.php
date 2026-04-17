<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
