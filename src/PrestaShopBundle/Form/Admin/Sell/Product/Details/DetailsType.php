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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Details;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Sell\Product\Options\ProductAttachmentsType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class DetailsType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    protected $isFeatureEnabled;

    /**
     * @var FormChoiceProviderInterface
     */
    private $productConditionChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $productConditionChoiceProvider
     * @param bool $isFeatureEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $productConditionChoiceProvider,
        bool $isFeatureEnabled
    ) {
        parent::__construct($translator, $locales);
        $this->productConditionChoiceProvider = $productConditionChoiceProvider;
        $this->isFeatureEnabled = $isFeatureEnabled;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('references', ReferencesType::class);

        if ($this->isFeatureEnabled) {
            $builder->add('features', FeaturesType::class);
        }

        $builder
            ->add('attachments', ProductAttachmentsType::class)
            ->add('show_condition', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Display condition on product page', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h3',
                'show_choices' => false,
                'inline_switch' => true,
                'modify_all_shops' => true,
            ])
            ->add('condition', ChoiceType::class, [
                'choices' => $this->productConditionChoiceProvider->getChoices(),
                'attr' => [
                    'class' => 'custom-select',
                ],
                'required' => false,
                // placeholder false is important to avoid empty option in select input despite required being false
                'placeholder' => false,
                'label' => false,
                'modify_all_shops' => true,
            ])
            ->add('customizations', CustomizationsType::class)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'required' => false,
                'label' => $this->trans('Details', 'Admin.Catalog.Feature'),
            ])
        ;
    }
}
