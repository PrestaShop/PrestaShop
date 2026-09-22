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
use PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store\ContactDetailsType;
use Symfony\Component\Form\FormInterface;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;

class ContactDetailsTypeTest extends FormListenerTestCase
{
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
     * These options are stored in ps_configuration, not in the ps_store columns, and legacy
     * applied no length limit to them: a phone in international format must still be savable.
     */
    public function testContactDetailsAreNotLimitedByTheStoreColumnLengths(): void
    {
        $form = $this->submitContactDetails([
            'phone' => '+33 1 23 45 67 89',
            'fax' => '+1 (555) 123-4567',
            'postcode' => 'SW1A 1AA-1234',
            'address1' => str_repeat('a', 300),
        ]);

        $this->assertSame([], $this->errorMessages($form));
    }

    /**
     * Legacy parity: isGenericName() rejects <>{} — the regex constraints must stay in place.
     */
    public function testInvalidCharactersAreStillRejected(): void
    {
        $form = $this->submitContactDetails(['city' => 'Paris {75}']);

        $messages = [];
        foreach ($form->get('city')->getErrors() as $error) {
            $messages[] = $error->getMessage();
        }

        $this->assertNotEmpty($messages, 'Braces must still be refused, as isGenericName() always did.');
        $this->assertStringContainsString('is invalid', implode(' ', $messages));
    }

    private function submitContactDetails(array $overrides): FormInterface
    {
        // No request cycle here, so there is no CSRF token to send: without this the root form
        // would carry an "invalid token" error that has nothing to do with what is under test.
        $form = $this->createForm(ContactDetailsType::class, ['csrf_protection' => false]);

        $form->submit($overrides + [
            'name' => 'My Shop',
            'email' => 'shop@example.com',
            'registration_number' => '',
            'address1' => '',
            'address2' => '',
            'postcode' => '',
            'city' => '',
            'id_country' => '',
            'id_state' => '',
            'phone' => '',
            'fax' => '',
        ]);

        return $form;
    }

    /**
     * @return string[]
     */
    private function errorMessages(FormInterface $form): array
    {
        $messages = [];
        foreach ($form->getErrors(true) as $error) {
            $messages[] = $error->getOrigin()?->getName() . ': ' . $error->getMessage();
        }

        return $messages;
    }
}
