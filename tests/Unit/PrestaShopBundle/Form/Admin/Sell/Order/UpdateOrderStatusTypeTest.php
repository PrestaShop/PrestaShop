<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Order\UpdateOrderStatusType;
use PrestaShopBundle\Form\Extension\AutoCompleteExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;

class UpdateOrderStatusTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $statusChoiceProvider = $this->createMock(ConfigurableFormChoiceProviderInterface::class);
        $statusChoiceProvider->method('getChoices')->willReturn([
            'Awaiting payment' => 1,
            'Payment accepted' => 2,
        ]);

        return [
            new PreloadedExtension(
                [new UpdateOrderStatusType($statusChoiceProvider, [])],
                [ChoiceType::class => [new AutoCompleteExtension()]]
            ),
        ];
    }

    public function testItShowsAnEmptyOptionWhenTheOrderHasNoStatus(): void
    {
        $view = $this->factory
            ->create(UpdateOrderStatusType::class, ['new_order_status_id' => 0])
            ->createView();

        // An empty placeholder makes the dropdown render an empty first option instead of
        // pre-selecting the first status when the order has no current status.
        $this->assertSame('', $view['new_order_status_id']->vars['placeholder']);
    }

    public function testItKeepsTheCurrentStatusSelectedWhenTheOrderHasOne(): void
    {
        $view = $this->factory
            ->create(UpdateOrderStatusType::class, ['new_order_status_id' => 2])
            ->createView();

        // No placeholder (Symfony maps placeholder=false to null in the view), so the order's
        // current status stays selected.
        $this->assertNull($view['new_order_status_id']->vars['placeholder']);
    }
}
