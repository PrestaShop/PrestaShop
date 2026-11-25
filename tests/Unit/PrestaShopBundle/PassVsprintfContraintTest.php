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

    /**
     * @dataProvider getLiteralPercentageTranslations
     */
    public function testLiteralPercentageIsValid(string $source, string $translated): void
    {
        $translation = (new Translation())
            ->setKey($source)
            ->setTranslation($translated);

        $this->validator->validate($translation, new PassVsprintf());

        $this->assertNoViolation();
    }

    public static function getLiteralPercentageTranslations(): iterable
    {
        yield 'newsletter translation' => [
            'Newsletter text',
            'Nouveautés, coups de cœur et idées cadeaux… Inscrivez-vous à notre newsletter et recevez 10 % de remise sur votre première commande.',
        ];
        yield 'English percentage' => ['Offer', 'Get 10% off your first order.'];
        yield 'compact percentage' => ['Offer', '10%discount'];
        yield 'multiple percentages' => ['Offer', '10 % de remise et 20 % sur votre prochaine commande.'];
        yield 'decimal percentage' => ['Offer', '10,5 % de remise.'];
        yield 'word with apostrophe' => ['Offer', "10 % d'économie."];
        yield 'word with curly apostrophe' => ['Offer', '10 % d’économie.'];
        yield 'non-breaking space' => ['Offer', "10\u{00A0}% de remise."];
        yield 'narrow non-breaking space' => ['Offer', "10\u{202F}% de remise."];
        yield 'percentage in source without format specifier' => ['Save 10%', 'Économisez 10 % sur votre commande.'];
        yield 'percentage at end of translation' => ['Offer', 'Remise de 10 %'];
    }

    /**
     * @dataProvider getInvalidPlaceholderTranslations
     */
    public function testPlaceholderMismatchIsStillInvalid(string $source, string $translated): void
    {
        $translation = (new Translation())
            ->setKey($source)
            ->setTranslation($translated);
        $constraint = new PassVsprintf();

        $this->validator->validate($translation, $constraint);

        $this->buildViolation($constraint->message)->assertRaised();
    }

    public static function getInvalidPlaceholderTranslations(): iterable
    {
        yield 'added string placeholder' => ['Offer', 'Remise pour %s'];
        yield 'added numeric placeholder' => ['Offer', 'Remise de %d'];
        yield 'added placeholder next to percentage' => ['Offer', '10 % de remise pour %s'];
        yield 'missing numbered placeholder' => ['Offer for %1$s', 'Offre pour les clients'];
        yield 'missing space-padded placeholder' => ['Offer for % d customers', 'Offre pour les clients'];
        yield 'unescaped percentage in sprintf translation' => ['Offer for %s', '10 % de remise pour %s'];
    }

    /**
     * @dataProvider getValidPlaceholderTranslations
     */
    public function testExistingPlaceholdersRemainValid(string $source, string $translated): void
    {
        $translation = (new Translation())
            ->setKey($source)
            ->setTranslation($translated);

        $this->validator->validate($translation, new PassVsprintf());

        $this->assertNoViolation();
    }

    public static function getValidPlaceholderTranslations(): iterable
    {
        yield 'numbered placeholders' => ['Offer for %1$s: %2$d', 'Offre de %2$d pour %1$s'];
        yield 'space-padded placeholder' => ['Offer for % d customers', 'Offre pour % d clients'];
        yield 'escaped percentage' => ['Save 10%% for %s', 'Économisez 10%% pour %s'];
        yield 'named placeholder' => ['Offer for %customer%', 'Offre pour %customer%'];
    }
}
