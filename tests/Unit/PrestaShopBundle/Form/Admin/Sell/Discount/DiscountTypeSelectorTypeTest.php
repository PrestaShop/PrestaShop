<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShopBundle\Form\Admin\Sell\Discount\DiscountTypeSelectorType;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountTypeSelectorTypeTest extends TypeTestCase
{
    private const LANG_ID = 2;

    protected function getExtensions(): array
    {
        // cart_rule_type_lang holds the default language's text for every language, which is what the
        // selector used to display: the names below are deliberately the English ones a French shop
        // gets after installing the language.
        $discountTypeRepository = $this->createMock(DiscountTypeRepository::class);
        $discountTypeRepository->method('getAllTypes')->willReturn([
            1 => $this->type(1, DiscountType::FREE_SHIPPING, 'On free shipping'),
            2 => $this->type(2, DiscountType::CART_LEVEL, 'On cart amount'),
            3 => $this->type(3, 'some_module_type', 'Remise du module'),
        ]);

        $languageContext = $this->createMock(LanguageContext::class);
        $languageContext->method('getId')->willReturn(self::LANG_ID);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $id): string => 'translated:' . $id
        );

        return [
            new PreloadedExtension(
                [new DiscountTypeSelectorType($discountTypeRepository, $languageContext, $translator, [])],
                []
            ),
        ];
    }

    public function testACoreTypeIsLabelledFromTheCatalogue(): void
    {
        $labels = $this->getChoiceLabels();

        $this->assertSame('translated:On free shipping', $labels[DiscountType::FREE_SHIPPING]);
        $this->assertSame('translated:On cart amount', $labels[DiscountType::CART_LEVEL]);
    }

    /**
     * A module-provided type has no catalogue entry, so its stored name is all there is.
     */
    public function testAModuleTypeKeepsItsStoredName(): void
    {
        $this->assertSame('Remise du module', $this->getChoiceLabels()['some_module_type']);
    }

    /**
     * @return array<string, string> discount type => label
     */
    private function getChoiceLabels(): array
    {
        $view = $this->factory->create(DiscountTypeSelectorType::class)->createView();

        $labels = [];
        foreach ($view['discount_type_selector']->vars['choices'] as $choiceView) {
            $labels[$choiceView->value] = $choiceView->label;
        }

        return $labels;
    }

    private function type(int $id, string $discountType, string $name): array
    {
        return [
            'id_cart_rule_type' => $id,
            'discount_type' => $discountType,
            'is_core' => true,
            'enabled' => true,
            'names' => [self::LANG_ID => $name],
            'descriptions' => [self::LANG_ID => $name],
        ];
    }
}
