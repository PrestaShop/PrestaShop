<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\NoTags;
use PrestaShop\PrestaShop\Core\ConstraintValidator\NoTagsValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class NoTagsValidatorTest extends ConstraintValidatorTestCase
{
    public function testItFailsWhenScriptTagsAreGiven()
    {
        $scriptTag = '<script></script>';

        $this->validator->validate($scriptTag, new NoTags());

        $this->buildViolation((new NoTags())->message)
            ->setParameter('%s', '"' . $scriptTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenHTMLTagsGiven()
    {
        $htmlTag = '<div class="btn">Button</div>';

        $this->validator->validate($htmlTag, new NoTags());

        $this->buildViolation((new NoTags())->message)
            ->setParameter('%s', '"' . $htmlTag . '"')
            ->assertRaised()
        ;
    }

    public function testItFailsWhenPHPTagsGiven()
    {
        $phpTag = '<?php $_SERVER = "crash"; ?>';

        $this->validator->validate($phpTag, new NoTags());

        $this->buildViolation((new NoTags())->message)
            ->setParameter('%s', '"' . $phpTag . '"')
            ->assertRaised()
        ;
    }

    protected function createValidator(): NoTagsValidator
    {
        return new NoTagsValidator();
    }
}
