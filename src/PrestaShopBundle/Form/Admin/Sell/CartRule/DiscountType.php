<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CartRule;

use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Form\ChoiceProvider\DiscountApplicationChoiceProvider;
use PrestaShopBundle\Form\Admin\Sell\CartRule\EventListener\DiscountListener;
use PrestaShopBundle\Form\Admin\Type\PriceReductionType;
use PrestaShopBundle\Form\Admin\Type\ProductSearchType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DiscountType extends TranslatorAwareType
{
    /**
     * @var DiscountListener
     */
    private $discountListener;

    /**
     * @var DiscountApplicationChoiceProvider
     */
    private $discountApplicationChoiceProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        DiscountListener $discountListener,
        DiscountApplicationChoiceProvider $discountApplicationChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->discountListener = $discountListener;
        $this->discountApplicationChoiceProvider = $discountApplicationChoiceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reduction', PriceReductionType::class, [
                'currency_select' => true,
                'label' => false,
            ])
            ->add('discount_application', ChoiceType::class, [
                // choices depends on reduction type data therefore are set in an event subscriber added bellow
                'choices' => [],
                'attr' => [
                    // these attributes are needed for js to change choices when reduction type changes
                    'data-amount-choices' => json_encode($this->getChoicesByType(Reduction::TYPE_AMOUNT)),
                    'data-percentage-choices' => json_encode($this->getChoicesByType(Reduction::TYPE_PERCENTAGE)),
                ],
            ])
            ->add('specific_product', ProductSearchType::class, [
                'include_combinations' => false,
                'row_attr' => [
                    'class' => 'specific-product-search-container',
                ],
            ])
            ->add('apply_to_discounted_products', SwitchType::class, [
                'label' => $this->trans('Apply to discounted products', 'Admin.Catalog.Feature'),
                'row_attr' => [
                    'class' => 'apply-to-discounted-products',
                ],
            ])
        ;

        $builder->addEventSubscriber($this->discountListener);
    }

    private function getChoicesByType(string $reductionType): array
    {
        return $this->discountApplicationChoiceProvider->getChoices([
            'reduction_type' => $reductionType,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'required' => false,
            'row_attr' => [
                'class' => 'discount-container',
            ],
            'disabling_switch' => true,
            'disabled_value' => static function (?array $data) {
                return empty($data['reduction']['value']);
            },
        ]);
    }
}
