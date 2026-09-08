<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Type\Material;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MaterialChoiceTableTypeTest extends TestCase
{
    /**
     * @dataProvider providerBuildView
     */
    public function testBuildView(
        array $viewData,
        array $choices,
        bool $displayTotalItems,
        array $expectedReturn
    ): void {
        $mockForm = $this
            ->getMockBuilder(FormInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockForm
            ->expects($this->once())
            ->method('getViewData')
            ->willReturn($viewData);

        $formView = new FormView();
        $materialChoiceTableForm = new MaterialChoiceTableType();

        $materialChoiceTableForm->buildView(
            $formView,
            $mockForm,
            [
                'choices' => $choices,
                'display_total_items' => $displayTotalItems,
            ]
        );

        $this->assertEquals(
            $expectedReturn,
            [
                $formView->vars['isCheckSelectAll'],
                $formView->vars['displayTotalItems'],
            ]
        );
    }

    public function providerBuildView(): array
    {
        return [
            [
                [1, 2, 3],
                [1, 2, 3],
                false,
                [true, false],
            ],
            [
                [1, 2],
                [1, 2, 3],
                true,
                [false, true],
            ],
        ];
    }

    public function testMultipleOptionIsEnabledByDefault(): void
    {
        $resolver = new OptionsResolver();
        $materialChoiceTableType = new MaterialChoiceTableType();

        $materialChoiceTableType->configureOptions($resolver);

        $options = $resolver->resolve();

        $this->assertTrue($options['multiple']);
        $this->assertTrue($options['expanded']);
        $this->assertFalse($options['display_total_items']);
    }

    public function testMultipleOptionCannotBeDisabled(): void
    {
        $resolver = new OptionsResolver();
        $materialChoiceTableType = new MaterialChoiceTableType();

        $materialChoiceTableType->configureOptions($resolver);

        $this->expectException(InvalidOptionsException::class);

        $resolver->resolve([
            'multiple' => false,
        ]);
    }
}
