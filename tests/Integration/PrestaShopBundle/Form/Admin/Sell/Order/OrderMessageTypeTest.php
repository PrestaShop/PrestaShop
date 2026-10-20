<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Form\Admin\Sell\Order\OrderMessageType;
use Tests\Integration\PrestaShopBundle\Form\AbstractFormTester;

class OrderMessageTypeTest extends AbstractFormTester
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
     * Selecting a predefined message on an order copies its text straight into this field, so this
     * form has to accept whatever the predefined message form accepts. If the two limits diverge,
     * picking a long predefined message fills the box with a value the box then refuses.
     */
    public function testTheMessageOnAnOrderAcceptsWhatAPredefinedMessageMayContain(): void
    {
        $message = str_repeat('a', 5000);

        $form = $this->createForm(OrderMessageType::class, ['csrf_protection' => false]);
        $form->submit(['message' => $message]);

        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[] = $error->getMessage();
        }

        self::assertTrue(
            $form->isValid(),
            sprintf('A %d character message was refused on an order: %s', strlen($message), implode(' | ', $errors))
        );
    }

    /**
     * Control, so the assertion above cannot pass because validation stopped running.
     */
    public function testAnEmptyMessageOnAnOrderIsStillRefused(): void
    {
        $form = $this->createForm(OrderMessageType::class, ['csrf_protection' => false]);
        $form->submit(['message' => '']);

        self::assertFalse($form->isValid(), 'An empty message was accepted on an order.');
    }
}
