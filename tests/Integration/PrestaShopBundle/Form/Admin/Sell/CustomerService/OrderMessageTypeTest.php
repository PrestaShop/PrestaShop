<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Sell\CustomerService;

use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Form\Admin\Sell\CustomerService\OrderMessageType;
use Symfony\Component\Form\FormInterface;
use Tests\Integration\PrestaShopBundle\Form\AbstractFormTester;

class OrderMessageTypeTest extends AbstractFormTester
{
    private const DEFAULT_LANG_ID = 1;

    protected function setUp(): void
    {
        parent::setUp();

        // A kernel booted outside a request has no shop or language context, and the
        // DefaultLanguage constraint on this form reads both.
        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = self::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopId(1);
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop(1));

        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setLanguageId(self::DEFAULT_LANG_ID);
        $languageContextBuilder->setDefaultLanguageId(self::DEFAULT_LANG_ID);
    }

    /**
     * A predefined message is stored in `order_message_lang`.`message`, a mediumtext column, and
     * OrderMessage's own model declares the matching size for that field. Only the form used to
     * hold it to a far shorter length, so a merchant could not write one longer than that.
     */
    public function testAPredefinedMessageLongerThanTwelveHundredCharactersIsAccepted(): void
    {
        $message = str_repeat('a', 5000);

        $form = $this->submitPredefinedMessage($message);

        self::assertTrue(
            $form->isValid(),
            sprintf('A %d character predefined message was refused: %s', strlen($message), $this->violations($form))
        );
        self::assertSame($message, $form->getData()['message'][self::DEFAULT_LANG_ID]);
    }

    /**
     * Control: the same form still refuses what it has always refused, so the assertion above is
     * about the length limit and not about validation having stopped running.
     */
    public function testAnEmptyPredefinedMessageIsStillRefused(): void
    {
        $form = $this->submitPredefinedMessage('');

        self::assertFalse($form->isValid(), 'An empty predefined message was accepted.');
    }

    private function submitPredefinedMessage(string $message): FormInterface
    {
        // No token is submitted here, so CSRF has to be off or every assertion below would be
        // about the token rather than about the field.
        $form = $this->createForm(OrderMessageType::class, ['csrf_protection' => false]);
        $form->submit([
            'name' => [self::DEFAULT_LANG_ID => 'Length probe'],
            'message' => [self::DEFAULT_LANG_ID => $message],
        ]);

        return $form;
    }

    private function violations(FormInterface $form): string
    {
        $messages = [];
        foreach ($form->getErrors(true) as $error) {
            $messages[] = $error->getMessage();
        }

        return $messages === [] ? '(no violation reported)' : implode(' | ', $messages);
    }
}
