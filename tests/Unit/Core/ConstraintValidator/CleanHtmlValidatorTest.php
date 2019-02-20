<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.20
 * Time: 14.52
 */

namespace Tests\Unit\Core\ConstraintValidator;


use PrestaShop\PrestaShop\Core\ConstraintValidator\CleanHtmlValidator;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class CleanHtmlValidatorTest extends ConstraintValidatorTestCase
{
    /**
     * @dataProvider getValuesWithScriptTags
     */
    public function testItFailsWhenScriptTagsAreGiven($incorrectValue)
    {
        $this->validator->validate($incorrectValue, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $incorrectValue . '"')
            ->assertRaised()
        ;
    }

    public function getValuesWithScriptTags()
    {
        yield ['<script></script>'];
        yield ['<script>'];
        yield ['<script'];
    }

    /**
     * @dataProvider getValuesWithJavascriptEvents
     */
    public function testItFailsWhenJavascriptEventsAreGiven($incorrectValue)
    {
        $this->validator->validate($incorrectValue, new CleanHtml());

        $this->buildViolation((new CleanHtml())->message)
            ->setParameter('%s', '"' . $incorrectValue . '"')
            ->assertRaised()
        ;
    }

    public function getValuesWithJavascriptEvents()
    {
        yield ['<a href="#" onchange="evilJavascriptIsCalled()"></a>'];
        yield ['onmousedown='];
    }

    protected function createValidator()
    {
        return new CleanHtmlValidator();
    }
}
