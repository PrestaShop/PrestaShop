<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\Product\Details;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Product\Details\DetailsType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class DetailsTypeTest extends TestCase
{
    /**
     * @dataProvider providerBuildForm
     *
     * @param bool $isFeatureEnabled
     * @param array $expectedChildren
     *
     * @return void
     */
    public function testBuildForm(bool $isFeatureEnabled, array $expectedChildren): void
    {
        $mockTranslatorInterface = $this
            ->getMockBuilder(TranslatorInterface::class)
            ->getMock();
        $mockFormBuilder = $this
            ->getMockBuilder(FormBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockFormChoiceProviderInterface = $this
            ->getMockBuilder(FormChoiceProviderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $children = [];

        $mockFormBuilder->method('add')->willReturnCallback(function (string $child) use (&$children, $mockFormBuilder) {
            $children[] = $child;

            return $mockFormBuilder;
        });
        $mockFormBuilder->method('all')->willReturnCallback(function () use (&$children) {
            return $children;
        });

        $formType = new DetailsType($mockTranslatorInterface, [], $mockFormChoiceProviderInterface, $isFeatureEnabled);
        $formType->buildForm($mockFormBuilder, []);

        $this->assertEquals($expectedChildren, $mockFormBuilder->all());
    }

    /**
     * @return array<array<bool|array<string>>>
     */
    public function providerBuildForm(): array
    {
        return [
            [
                true,
                [
                    'references',
                    'features',
                    'attachments',
                    'show_condition',
                    'condition',
                    'customizations',
                ],
            ],
            [
                false,
                [
                    'references',
                    'attachments',
                    'show_condition',
                    'condition',
                    'customizations',
                ],
            ],
        ];
    }
}
