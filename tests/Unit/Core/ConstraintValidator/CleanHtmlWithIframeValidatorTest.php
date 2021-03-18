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

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\CleanHtmlValidator;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CleanHtmlWithIframeValidatorTest extends ConstraintValidatorTestCase
{
    public function testItFailsWhenScriptTagsAreGiven()
    {
        $scriptTag = '<script></script>';

        $this->validator->validate($scriptTag, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $scriptTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenJavascriptEventsAreGiven()
    {
        $htmlTag = '<a href="#" onchange="evilJavascriptIsCalled()"></a>';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testItSucceedsWhenIframeIsGiven()
    {
        $htmlTag = '<iframe src="catvideo.html" /></iframe>';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->assertNoViolation();
        $this->context->getViolations();
    }

    public function testItSucceedsWhenFormIsGiven()
    {
        $htmlTag = '<form>';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->assertNoViolation();
        $this->context->getViolations();
    }

    public function testItSucceedsWhenInputIsGiven()
    {
        $htmlTag = '<input name="your-card-number">';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->assertNoViolation();
        $this->context->getViolations();
    }

    public function testItSucceedsWhenEmbedIsGiven()
    {
        $htmlTag = '<embed type="image/jpg" src="funny_cat.jpg" width="300" height="200">';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->assertNoViolation();
        $this->context->getViolations();
    }

    public function testItSucceedsWhenObjectIsGiven()
    {
        $htmlTag = '<object data="funny_cat.jpg" width="300" height="200"></object> ';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->assertNoViolation();
        $this->context->getViolations();
    }

    protected function createValidator()
    {
        return new CleanHtmlValidator(true);
    }
}
