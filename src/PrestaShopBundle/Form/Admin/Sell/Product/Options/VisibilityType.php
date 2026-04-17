<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use PrestaShop\PrestaShop\Core\Form\FormChoiceAttributeProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class VisibilityType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface&FormChoiceAttributeProviderInterface
     */
    private $productVisibilityChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface&FormChoiceAttributeProviderInterface $productVisibilityChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $productVisibilityChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->productVisibilityChoiceProvider = $productVisibilityChoiceProvider;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visibility', ChoiceType::class, [
                'label' => false,
                'choices' => $this->productVisibilityChoiceProvider->getChoices(),
                'choice_attr' => $this->productVisibilityChoiceProvider->getChoicesAttributes(),
                'expanded' => true,
                'required' => false,
                // placeholder false is important to avoid empty option in radio select despite required being false
                'placeholder' => false,
                'column_breaker' => true,
                'modify_all_shops' => true,
                'help' => '', // should be set to enable help block
                'help_attr' => ['class' => 'js-visibility-description'],
            ])
            ->add('available_for_order', SwitchType::class, [
                'label' => $this->trans('Available for order', 'Admin.Catalog.Feature'),
                'required' => false,
                'modify_all_shops' => true,
            ])
            ->add('show_price', SwitchType::class, [
                'label' => $this->trans('Show price', 'Admin.Catalog.Feature'),
                'required' => false,
                'modify_all_shops' => true,
                'row_attr' => [
                    'class' => 'show-price-switch-container',
                ],
            ])
            ->add('online_only', SwitchType::class, [
                'label' => $this->trans('Web only (not sold in your retail store)', 'Admin.Catalog.Feature'),
                'required' => false,
                'modify_all_shops' => true,
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Visibility', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'label_subtitle' => $this->trans('Where do you want your product to appear?', 'Admin.Catalog.Feature'),
            'required' => false,
            'columns_number' => 4,
        ]);
    }
}
