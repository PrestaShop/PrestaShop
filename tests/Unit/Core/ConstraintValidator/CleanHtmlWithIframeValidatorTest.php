<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
