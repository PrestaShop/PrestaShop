<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier\Type;

use PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier\Type\CostsRangeType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * A negative shipping cost is accepted by the database and then applied to the cart, where it
 * behaves as a discount. The range bounds were already guarded, the price is guarded here too.
 */
class CostsRangeTypeTest extends TypeTestCase
{
    protected function getExtensions(): array
    {
        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnArgument(0);

        return [
            new PreloadedExtension([new CostsRangeType($translator, [])], []),
            new ValidatorExtension(Validation::createValidator()),
        ];
    }

    /**
     * @dataProvider getAcceptedPrices
     */
    public function testItAcceptsAPriceThatIsPositiveOrZero(string $price): void
    {
        $this->assertTrue($this->submitPrice($price)->isValid());
    }

    /**
     * @dataProvider getRejectedPrices
     */
    public function testItRejectsANegativePrice(string $price): void
    {
        $form = $this->submitPrice($price);

        $this->assertFalse($form->isValid());
        $this->assertCount(1, $form->get('price')->getErrors());
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function getAcceptedPrices(): array
    {
        return [['0'], ['0.00'], ['5'], ['12.34']];
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function getRejectedPrices(): array
    {
        return [['-5'], ['-0.01'], ['-12.34']];
    }

    private function submitPrice(string $price): \Symfony\Component\Form\FormInterface
    {
        $form = $this->factory->create(CostsRangeType::class);
        $form->submit([
            'from' => '0',
            'to' => '10',
            'price' => $price,
        ]);

        return $form;
    }
}
