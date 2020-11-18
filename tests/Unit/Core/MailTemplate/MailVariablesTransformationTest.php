<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
}
