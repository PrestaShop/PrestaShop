<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Shop;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\ColorPickerType;
use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShopType extends TranslatorAwareType
{
    private const NAME_MAX_LENGTH = 64;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly ConfigurableFormChoiceProviderInterface $shopGroupChoiceProvider,
        private readonly FormChoiceProviderInterface $rootCategoryChoiceProvider,
        private readonly FormChoiceProviderInterface $categoryTreeChoiceProvider,
        private readonly FormChoiceProviderInterface $themeChoiceProvider,
        private readonly FormChoiceProviderInterface $shopChoiceProvider,
        private readonly FormChoiceProviderInterface $importDataChoiceProvider,
        private readonly LegacyContext $legacyContext,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $store = $builder->create('store', CardType::class, [
            'label' => $this->trans('Store', 'Admin.Global'),
            'icon' => 'store',
            'inherit_data' => true,
        ])
            ->add('name', TextType::class, [
                'label' => $this->trans('Store name', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('This field does not refer to the shop name visible in the front office.', 'Admin.Shopparameters.Help')
                    . ' ' . $this->trans(
                        'Follow [1]this link[/1] to edit the shop name used on the front office.',
                        'Admin.Shopparameters.Help',
                        [
                            '[1]' => sprintf('<a href="%s">', $this->legacyContext->getAdminLink('AdminStores') . '#store_fieldset_general'),
                            '[/1]' => '</a>',
                        ]
                    ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [sprintf('"%s"', $this->trans('Store name', 'Admin.Shopparameters.Feature'))]
                        ),
                    ]),
                    new TypedRegex(['type' => TypedRegex::TYPE_GENERIC_NAME]),
                    new Length([
                        'max' => self::NAME_MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => self::NAME_MAX_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('color', ColorPickerType::class, [
                'label' => $this->trans('Color', 'Admin.Catalog.Feature'),
                'required' => false,
                'empty_data' => '',
                'help' => $this->trans('It will only be applied to the multistore header to highlight your shop context.', 'Admin.Shopparameters.Feature'),
            ])
            ->add('shop_group_id', ChoiceType::class, [
                'label' => $this->trans('Shop group', 'Admin.Shopparameters.Feature'),
                'choices' => $this->shopGroupChoiceProvider->getChoices([
                    'exclude_groups_sharing_data_with_dependencies' => $options['is_edition'] && !$options['shop_group_locked'],
                ]),
                'translation_domain' => false,
                'disabled' => $options['shop_group_locked'],
                'help' => $this->getShopGroupHelp($options),
            ])
            ->add('root_category_id', ChoiceType::class, [
                'label' => $this->trans('Category root', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'This is the root category of the store that you\'ve created. To define a new root category for your store, [1]please click here[/1].',
                    'Admin.Shopparameters.Help',
                    [
                        '[1]' => sprintf('<a href="%s" target="_blank">', $this->legacyContext->getAdminLink('AdminCategories', true, ['addcategoryroot' => 1])),
                        '[/1]' => '</a>',
                    ]
                ),
                'choices' => $this->rootCategoryChoiceProvider->getChoices(),
                'translation_domain' => false,
            ])
            ->add('category_ids', MaterialChoiceTreeType::class, [
                'label' => $this->trans('Associated categories', 'Admin.Catalog.Feature'),
                'help' => $this->trans('By selecting associated categories, you are choosing to share the categories between shops. Once associated between shops, any alteration of this category will impact every shop.', 'Admin.Shopparameters.Help'),
                'choices_tree' => $this->categoryTreeChoiceProvider->getChoices()[0]['children'] ?? [],
                'choice_value' => 'id_category',
                'choice_label' => 'name',
                'multiple' => true,
            ])
            ->add('theme_name', ChoiceType::class, [
                'label' => $this->trans('Theme', 'Admin.Design.Feature'),
                'choices' => $this->themeChoiceProvider->getChoices(),
                'translation_domain' => false,
            ]);

        $builder->add($store);

        if ($options['is_edition']) {
            return;
        }

        $builder->add(
            $builder->create('import', CardType::class, [
                'label' => $this->trans('Import data from another shop', 'Admin.Advparameters.Feature'),
                'icon' => 'cloud_download',
                'inherit_data' => true,
            ])
                ->add('use_import_data', SwitchType::class, [
                    'label' => $this->trans('Import data', 'Admin.Advparameters.Feature'),
                ])
                ->add('source_shop_id', ChoiceType::class, [
                    'label' => $this->trans('Choose the source store', 'Admin.Advparameters.Feature'),
                    'choices' => $this->shopChoiceProvider->getChoices(),
                    'translation_domain' => false,
                    'attr' => ['data-root-category-ids' => json_encode($options['source_shop_root_category_ids'])],
                ])
                ->add('imported_data', ChoiceType::class, [
                    'label' => $this->trans('Choose data to import', 'Admin.Advparameters.Feature'),
                    'help' => $this->trans('Use this option to associate data (products, modules, etc.) the same way for each selected shop.', 'Admin.Advparameters.Help'),
                    'choices' => $this->importDataChoiceProvider->getChoices(),
                    'translation_domain' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'required' => false,
                ])
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'is_edition' => false,
                'shop_group_locked' => false,
                'source_shop_root_category_ids' => [],
            ])
            ->setAllowedTypes('is_edition', 'bool')
            ->setAllowedTypes('shop_group_locked', 'bool')
            ->setAllowedTypes('source_shop_root_category_ids', 'int[]');
    }

    private function getShopGroupHelp(array $options): string
    {
        if ($options['shop_group_locked']) {
            return $this->trans('You can\'t edit the shop group because the current shop belongs to a group with the "share" option enabled.', 'Admin.Shopparameters.Help');
        }

        if ($options['is_edition']) {
            return $this->trans('You can only move your shop to a shop group with all "share" options disabled -- or to a shop group with no customers/orders.', 'Admin.Shopparameters.Notification');
        }

        return $this->trans('Warning: You won\'t be able to change the group of this shop if this shop belongs to a group with one of these options activated: Share Customers, Share Quantities or Share Orders.', 'Admin.Shopparameters.Notification');
    }
}
