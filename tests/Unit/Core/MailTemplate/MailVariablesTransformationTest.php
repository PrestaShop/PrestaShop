<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\MailVariablesTransformation;

class MailVariablesTransformationTest extends TestCase
{
    public function testConstructor()
    {
        $transformation = new MailVariablesTransformation(MailTemplateInterface::HTML_TYPE);
        $this->assertNotNull($transformation);

        $transformation = new MailVariablesTransformation(MailTemplateInterface::TXT_TYPE);
        $this->assertNotNull($transformation);
    }

    public function testApply()
    {
        $template = 'Hello {firstname} {lastname} how are you?';
        $transformation = new MailVariablesTransformation(MailTemplateInterface::HTML_TYPE);
        $layoutVariables = [
            'templateVars' => [
                '{firstname}' => 'John',
                '{lastname}' => 'Doe',
                'how are you' => 'wasup mate',
            ],
        ];
        $transformedTemplate = $transformation->apply($template, $layoutVariables);
        $this->assertEquals('Hello John Doe wasup mate?', $transformedTemplate);
    }

    public function testApplyViaConstructor()
    {
        $template = 'Hello {firstname} {lastname} how are you?';
        $transformation = new MailVariablesTransformation(MailTemplateInterface::HTML_TYPE, [
            '{firstname}' => 'John',
            '{lastname}' => 'Doe',
            'how are you' => 'wasup mate',
        ]);
        $transformedTemplate = $transformation->apply($template, []);
        $this->assertEquals('Hello John Doe wasup mate?', $transformedTemplate);
    }
}
