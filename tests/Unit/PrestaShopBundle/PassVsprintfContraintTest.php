<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle;

use PrestaShopBundle\Entity\Translation;
use PrestaShopBundle\Translation\Constraints\PassVsprintf;
use PrestaShopBundle\Translation\Constraints\PassVsprintfValidator;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PassVsprintfContraintTest extends ConstraintValidatorTestCase
{
    protected function createValidator()
    {
        return new PassVsprintfValidator();
    }

    public function testEmptyTranslationIsValid()
    {
        $translation = (new Translation())
            ->setKey('')
            ->setTranslation('');
        $this->validator->validate($translation, new PassVsprintf());

        $this->assertNoViolation();
    }

    public function testTranslationIsValid()
    {
        $translation = (new Translation())
            ->setKey('List of products by brand %s')
            ->setTranslation('Liste des produits de la marque %s');
        $this->validator->validate($translation, new PassVsprintf());

        $this->assertNoViolation();
    }

    public function testNotValid()
    {
        $translation = (new Translation())
            ->setKey('List of products by brand %s')
            ->setTranslation('Liste des produits de la marque nope');
        $constraint = new PassVsprintf();

        $this->validator->validate($translation, $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }
}
