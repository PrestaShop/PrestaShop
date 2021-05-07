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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Admin\Type\UnavailableType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * This form class is responsible to generate the product options form.
 */
class OptionsType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $productConditionChoiceProvider;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $productConditionChoiceProvider
     * @param LegacyContext $legacyContext
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $productConditionChoiceProvider,
        LegacyContext $legacyContext
    ) {
        parent::__construct($translator, $locales);
        $this->productConditionChoiceProvider = $productConditionChoiceProvider;
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('visibility', VisibilityType::class)
            ->add('tags', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Tags', 'Admin.Catalog.Feature'),
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                    ],
                    'attr' => [
                        'class' => 'js-taggable-field',
                        'placeholder' => $this->trans('Use a comma to create separate tags. E.g.: dress, cotton, party dresses.', 'Admin.Catalog.Help'),
                    ],
                    'required' => false,
                ],
                'alert_title' => $this->trans('Tags are meant to help your customers find your products via the search bar.', 'Admin.Catalog.Help'),
                'alert_message' => [
                    $this->trans('Choose terms and keywords that your customers will use to search for this product and make sure you are consistent with the tags you may have already used.', 'Admin.Catalog.Help'),
                    $this->trans('You can manage tag aliases in the [1]Search section[/1]. If you add new tags, you have to rebuild the index.', 'Admin.Catalog.Help', [
                        '[1]' => sprintf(
                            '<a target="_blank" href="%s">',
                            $this->legacyContext->getAdminLink('AdminSearchConf')
                        ),
                        '[/1]' => '</a>',
                    ]),
                ],
            ])
            ->add('condition', ChoiceType::class, [
                'choices' => $this->productConditionChoiceProvider->getChoices(),
                'attr' => [
                    'class' => 'custom-select',
                ],
                'required' => false,
                // placeholder false is important to avoid empty option in select input despite required being false
                'placeholder' => false,
                'label' => $this->trans('Condition', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'label_help_box' => $this->trans('Not all shops sell new products. This option enables you to indicate the condition of the product. It can be required on some marketplaces.', 'Admin.Catalog.Help'),
            ])
            ->add('show_condition', SwitchType::class, [
                'required' => false,
                'label' => $this->trans('Display condition on product page', 'Admin.Catalog.Feature'),
            ])
            ->add('references', ReferencesType::class)
            ->add('customizations', CustomizationsType::class)
            ->add('attached_files', UnavailableType::class, [
                'label' => $this->trans('Attached files', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
            ])
            ->add('suppliers', SuppliersType::class)
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
            'label' => false,
            // Suppliers can be removed so there might be extra data in the request during type switching
            'allow_extra_fields' => true,
        ]);
    }
}
