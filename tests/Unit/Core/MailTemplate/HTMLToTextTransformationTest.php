<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\HTMLToTextTransformation;

class HTMLToTextTransformationTest extends TestCase
{
    public function testConstructor()
    {
        $transformation = new HTMLToTextTransformation();
        $this->assertNotNull($transformation);
    }

    public function testSetters()
    {
        $transformation = new HTMLToTextTransformation();

        $languageMock = $this->getMockBuilder(LanguageInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->assertEquals($transformation, $transformation->setLanguage($languageMock));
    }

    public function testApply()
    {
        $htmlTemplate = file_get_contents(realpath(__DIR__ . '/../../Resources/mails/html/account.html'));
        $txtTemplate = file_get_contents(realpath(__DIR__ . '/../../Resources/mails/txt/account.txt'));

        $transformation = new HTMLToTextTransformation();
        $transformedTemplate = $transformation->apply($htmlTemplate, []);
        $this->assertEquals($txtTemplate, $transformedTemplate);
    }
}
