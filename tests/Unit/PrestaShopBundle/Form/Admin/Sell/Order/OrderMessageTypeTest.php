<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Order\OrderMessageType;
use PrestaShopBundle\Form\Extension\AutoCompleteExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\Traits\ValidatorExtensionTrait;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class OrderMessageTypeTest extends TypeTestCase
{
    use ValidatorExtensionTrait;

    protected function getExtensions(): array
    {
        $nameChoiceProvider = $this->createMock(FormChoiceProviderInterface::class);
        $nameChoiceProvider->method('getChoices')->willReturn([
            'Delay' => 1,
            'Out of stock' => 2,
        ]);

        $messageChoiceProvider = $this->createMock(ConfigurableFormChoiceProviderInterface::class);
        $messageChoiceProvider->method('getChoices')->willReturn([
            1 => 'We are sorry for the delay.',
            2 => 'This product is out of stock.',
        ]);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return [
            $this->getValidatorExtension(),
            new PreloadedExtension(
                [new OrderMessageType($nameChoiceProvider, $messageChoiceProvider, $translator)],
                [ChoiceType::class => [new AutoCompleteExtension()]]
            ),
        ];
    }

    public function testThePredefinedMessageDropdownIsSearchable(): void
    {
        $attributes = $this->buildView()['order_message']->vars['attr'];

        // The UI kit turns a select carrying this attribute into a select2, which is what
        // gives the predefined messages a search box.
        $this->assertSame('select2', $attributes['data-toggle']);
    }

    public function testTheSearchBoxOnlyAppearsOnceTheListIsLongEnough(): void
    {
        $attributes = $this->buildView()['order_message']->vars['attr'];

        // Same threshold as every other autocompleted dropdown of the back office, so a shop
        // with a handful of messages is not given a search box it does not need.
        $this->assertSame(7, $attributes['data-minimumResultsForSearch']);
    }

    public function testTheOtherFieldsAreLeftAlone(): void
    {
        $view = $this->buildView();

        $this->assertArrayNotHasKey('data-toggle', $view['message']->vars['attr']);
        $this->assertArrayNotHasKey('data-toggle', $view['is_displayed_to_customer']->vars['attr']);
    }

    private function buildView(): \Symfony\Component\Form\FormView
    {
        return $this->factory->create(OrderMessageType::class)->createView();
    }
}
