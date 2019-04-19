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
use PrestaShop\PrestaShop\Core\MailTemplate\Transformation\HTMLCleanTransformation;

class HTMLCleanTransformationTest extends TestCase
{
    public function testConstructor()
    {
        $transformation = new HTMLCleanTransformation();
        $this->assertNotNull($transformation);
    }

    public function testRemoveTxtContent()
    {
        $htmlContent = '<div>Random content <span data-text-only=1>Txt content</span></div>';
        $transformation = new HTMLCleanTransformation();
        $cleanHtml = $transformation->apply($htmlContent, []);
        $this->assertEquals('<html><body><div>Random content </div></body></html>', $cleanHtml);
    }

    public function testReplaceHtmlOnly()
    {
        $htmlContent = '<html-only><ul><li>Html list</li></ul></html-only>';
        $transformation = new HTMLCleanTransformation();
        $cleanHtml = $transformation->apply($htmlContent, []);
        $this->assertEquals('<html><body><ul><li>Html list</li></ul></body></html>', $cleanHtml);
    }

    public function testReplaceMjRaw()
    {
        $htmlContent = '<mj-raw><ul><li>Html list</li></ul></mj-raw>';
        $transformation = new HTMLCleanTransformation();
        $cleanHtml = $transformation->apply($htmlContent, []);
        $this->assertEquals('<html><body><ul><li>Html list</li></ul></body></html>', $cleanHtml);
    }

    public function testApply()
    {
        $htmlTemplate = file_get_contents(realpath(__DIR__ . '/../../Resources/mails/html/account.html'));
        $cleanTemplate = file_get_contents(realpath(__DIR__ . '/../../Resources/mails/html/clean-account.html'));

        $transformation = new HTMLCleanTransformation();
        $transformedTemplate = $transformation->apply($htmlTemplate, []);
        $this->assertEquals($cleanTemplate, $transformedTemplate);
    }
}
