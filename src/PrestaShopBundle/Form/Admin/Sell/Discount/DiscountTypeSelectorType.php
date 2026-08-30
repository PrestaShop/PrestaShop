<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShop\PrestaShop\Adapter\Discount\Repository\DiscountTypeRepository;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Domain\Discount\ValueObject\DiscountType;
use PrestaShopBundle\Form\Admin\Type\EnrichedChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountTypeSelectorType extends TranslatorAwareType
{
    private const ICON_MAP = [
        DiscountType::CART_LEVEL => ['icon' => 'shopping_cart', 'help' => 'Apply on total cart'],
        DiscountType::PRODUCT_LEVEL => ['icon' => 'shoppingmode', 'help' => 'Apply on catalog products'],
        DiscountType::FREE_GIFT => ['icon' => 'card_giftcard', 'help' => 'Apply on free gift'],
        DiscountType::FREE_SHIPPING => ['icon' => 'local_shipping', 'help' => 'Apply on shipping fees'],
        DiscountType::ORDER_LEVEL => ['icon' => 'article', 'help' => 'Apply on cart and shipping fees'],
    ];

    public function __construct(
        private readonly DiscountTypeRepository $discountTypeRepository,
        private readonly LanguageContext $languageContext,
        TranslatorInterface $translator,
        array $locales,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $currentLangId = $this->languageContext->getId();
        $choices = [];
        $choiceAttr = [];

        foreach ($this->discountTypeRepository->getAllTypes() as $type) {
            // Disabled temporarily, because of infinite loop issue with this kind of discount. See issue #39419
            // Linked to https://github.com/PrestaShop/PrestaShop/issues/42209
            if (!$type['enabled'] || $type['discount_type'] === DiscountType::ORDER_LEVEL) {
                continue;
            }
            $discountTypeValue = $type['discount_type'];
            $name = $type['names'][$currentLangId] ?? (reset($type['names']) ?: $discountTypeValue);
            $iconData = self::ICON_MAP[$discountTypeValue] ?? [];

            $choices[$name] = $discountTypeValue;
            $choiceAttr[$name] = [
                'icon' => $iconData['icon'] ?? '',
                'help' => isset($iconData['help']) ? $this->trans($iconData['help'], 'Admin.Catalog.Feature') : '',
                'badge-label' => $type['is_core'] ? $this->trans('Core', 'Admin.Catalog.Feature') : null,
            ];
        }

        $builder->add('discount_type_selector', EnrichedChoiceType::class, [
            'label' => $this->trans('This type cannot be modified after being saved.', 'Admin.Catalog.Feature'),
            'required' => false,
            'placeholder' => null,
            'choices' => $choices,
            'choice_attr' => $choiceAttr,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => false,
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit_base.html.twig',
        ]);
    }
}
