<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Order\CartSummaryType;
use PrestaShopBundle\Form\Extension\AutoCompleteExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class CartSummaryTypeTest extends TypeTestCase
{
    private const DELETED_STATE_ID = 3;

    protected function getExtensions(): array
    {
        $orderStatesChoiceProvider = $this->createMock(ConfigurableFormChoiceProviderInterface::class);
        $orderStatesChoiceProvider->method('getChoices')->willReturnCallback(
            static function (array $options = []): array {
                $choices = [
                    'Awaiting payment' => 1,
                    'Payment accepted' => 2,
                ];
                if ($options['include_deleted'] ?? true) {
                    $choices['Old status (deleted)'] = self::DELETED_STATE_ID;
                }

                return $choices;
            }
        );

        $paymentModulesChoiceProvider = $this->createMock(FormChoiceProviderInterface::class);
        $paymentModulesChoiceProvider->method('getChoices')->willReturn(['ps_checkpayment' => 'Payments by check']);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return [
            new PreloadedExtension(
                [new CartSummaryType($orderStatesChoiceProvider, $paymentModulesChoiceProvider, $translator)],
                [ChoiceType::class => [new AutoCompleteExtension()]]
            ),
        ];
    }

    public function testDeletedOrderStatesAreNotOffered(): void
    {
        $view = $this->factory->create(CartSummaryType::class)->createView();

        $offeredStateIds = array_map(
            static fn ($choice) => (int) $choice->value,
            $view['order_state']->vars['choices']
        );

        $this->assertSame([1, 2], array_values($offeredStateIds));
    }

    public function testDeletedOrderStateIsRejectedOnSubmit(): void
    {
        $form = $this->factory->create(CartSummaryType::class);
        $form->submit([
            'cart_id' => '1',
            'payment_module' => 'ps_checkpayment',
            'order_state' => (string) self::DELETED_STATE_ID,
        ]);

        $this->assertFalse($form->get('order_state')->isValid());
    }
}
