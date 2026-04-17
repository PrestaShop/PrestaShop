<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\CleanHtmlValidator;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CleanHtmlValidatorTest extends ConstraintValidatorTestCase
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

    public function testItFailsWhenAttributeStartingWithOnIsGiven()
    {
        $htmlTag = '<a href="#" onanything="evilJavascriptIsCalled()"></a>';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testCaseInsensitiveOnEventAttributeDetection()
    {
        $htmlTag = '<a href="#" oNnotexi="evilJavascriptIsCalled()"></a>';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenIframeIsGiven()
    {
        $htmlTag = '<iframe src="catvideo.html" /></iframe>';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenIframeWithSpacesIsGiven()
    {
        $htmlTag = '< iframe >';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenFormIsGiven()
    {
        $htmlTag = '<form>';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenInputIsGiven()
    {
        $htmlTag = '<input name="your-card-number">';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenEmbedIsGiven()
    {
        $htmlTag = '<embed type="image/jpg" src="funny_cat.jpg" width="300" height="200">';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenObjectIsGiven()
    {
        $htmlTag = '<object data="funny_cat.jpg" width="300" height="200"></object> ';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testSucceedsWithPlainWords()
    {
        $htmlTag = '/form input > embed object iframe';

        $this->validator->validate($htmlTag, new CleanHtml());

        $this->assertNoViolation();
        $this->context->getViolations();
    }

    public function testItSucceedsWhenRegularAttributeIsGiven()
    {
        $htmlTag = '<div randomattribute="blabla">test</div>';
        $this->validator->validate($htmlTag, new CleanHtml());

        $this->assertNoViolation();
        $this->context->getViolations();
    }

    public function testSucceedsWithSpaces()
    {
        $htmlTag = '<div

randomattribute="blabla"   attributewithoutvalue

        randomattr="random value">

</div>';
        $this->validator->validate($htmlTag, new CleanHtml());

        $this->assertNoViolation();
    }

    public function itFailsToHaveOnAttributeWithRandomSpacesAndLines()
    {
        $htmlTag = '<div
randomattribute="blabla"

    onbidule="test" attributewithoutvalue

        randomattr="random value">test

        </div>';

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;

        $this->context->getViolations();
    }

    public function itFailsWithRLOInjection()
    {
        $htmlTag = '‮<img src=x onerror="alert(\'img\')">';

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;

        $this->context->getViolations();
    }

    protected function createValidator()
    {
        return new CleanHtmlValidator(false);
    }
}
