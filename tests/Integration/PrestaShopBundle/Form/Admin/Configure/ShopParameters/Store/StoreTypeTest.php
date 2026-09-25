<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store;

use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store\StoreType;
use Symfony\Component\Form\FormInterface;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

class StoreTypeTest extends FormListenerTestCase
{
    private const FRANCE_ID = 8; // zip_code_format: NNNNN
    private const NETHERLANDS_ID = 13; // zip_code_format: NNNN LL
    private const IRELAND_ID = 26; // no zip_code_format, does not need a zip code

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopId(1);
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));

        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(1);
        $languageContextBuilder->setDefaultLanguageId(1);
    }

    /**
     * Legacy parity: AdminStoresController::postProcess() rejects a postcode that does not match
     * Country::$zip_code_format, so the migrated form must do the same.
     */
    public function testPostcodeNotMatchingTheCountryFormatIsRejected(): void
    {
        $form = $this->submitStore(self::FRANCE_ID, 'ABCDE');

        $this->assertPostcodeError($form, 'Your Zip/Postal code is incorrect.');
    }

    public function testPostcodeMatchingTheCountryFormatIsAccepted(): void
    {
        $form = $this->submitStore(self::FRANCE_ID, '75001');

        $this->assertNoPostcodeError($form);
    }

    /**
     * The constraint has to be rebuilt from the submitted country: the form is built with the
     * shop's context country (or the country the store was saved with), so validating against
     * that one lets a postcode belonging to a different format through.
     */
    public function testPostcodeIsValidatedAgainstTheSubmittedCountryNotTheContextCountry(): void
    {
        // Valid for France (NNNNN), invalid for the submitted country (NNNN LL).
        $form = $this->submitStore(self::NETHERLANDS_ID, '12345');

        $this->assertPostcodeError($form, 'Your Zip/Postal code is incorrect.');
    }

    public function testPostcodeMatchingTheSubmittedCountryFormatIsAccepted(): void
    {
        // Invalid for France (NNNNN), valid for the submitted country (NNNN LL).
        $form = $this->submitStore(self::NETHERLANDS_ID, '1234 AB');

        $this->assertNoPostcodeError($form);
    }

    public function testPostcodeIsOptionalForACountryWithoutFormat(): void
    {
        $form = $this->submitStore(self::IRELAND_ID, '');

        $this->assertNoPostcodeError($form);
    }

    private function submitStore(int $countryId, string $postcode): FormInterface
    {
        $form = $this->createForm(StoreType::class);

        $form->submit([
            'name' => [1 => 'Dade County'],
            'address1' => [1 => '3030 SW 8th St'],
            'address2' => [1 => ''],
            'postcode' => $postcode,
            'city' => 'Miami',
            'id_country' => (string) $countryId,
            'id_state' => '',
            'latitude' => '25.765005',
            'longitude' => '-80.243797',
            'phone' => '',
            'fax' => '',
            'email' => '',
            'note' => [1 => ''],
            'active' => '1',
            'hours' => [1 => array_fill(0, 7, '')],
        ]);

        return $form;
    }

    private function assertPostcodeError(FormInterface $form, string $expectedMessage): void
    {
        $messages = $this->postcodeErrorMessages($form);

        $this->assertNotEmpty($messages, 'Expected the postcode to be rejected.');
        $this->assertStringContainsString($expectedMessage, implode(' ', $messages));
    }

    private function assertNoPostcodeError(FormInterface $form): void
    {
        $this->assertSame([], $this->postcodeErrorMessages($form));
    }

    /**
     * @return string[]
     */
    private function postcodeErrorMessages(FormInterface $form): array
    {
        $messages = [];
        foreach ($form->get('postcode')->getErrors() as $error) {
            $messages[] = $error->getMessage();
        }

        return $messages;
    }
}
