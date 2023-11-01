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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Combination;

use Currency;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\Reference;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Form\Admin\Type\ButtonCollectionType;
use PrestaShopBundle\Form\Admin\Type\DeltaQuantityType;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\ImagePreviewType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class CombinationItemType extends TranslatorAwareType
{
    /**
     * Limit of action buttons to show before they are wrapped into a dropdown
     */
    private const INLINE_ACTIONS_LIMIT = 1;

    /**
     * @var Currency
     */
    protected $defaultCurrency;

    /**
     * @var FeatureInterface
     */
    private $multiStoreFeature;

    /**
     * @var int
     */
    private $contextShopId;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        Currency $defaultCurrency,
        FeatureInterface $multiStoreFeature,
        int $contextShopId
    ) {
        parent::__construct($translator, $locales);
        $this->defaultCurrency = $defaultCurrency;
        $this->multiStoreFeature = $multiStoreFeature;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->multiStoreFeature->isActive()) {
            $deleteItemMessage = $this->trans('Delete selected item from current store?', 'Admin.Notifications.Warning');
        } else {
            $deleteItemMessage = $this->trans('Delete selected item?', 'Admin.Notifications.Warning');
        }

        $actionButtons = [
            'edit' => [
                'type' => IconButtonType::class,
                'options' => [
                    'label' => $this->trans('Edit', 'Admin.Actions'),
                    'icon' => 'mode_edit',
                    'attr' => [
                        'class' => 'edit-combination-item tooltip-link',
                        'data-toggle' => 'pstooltip',
                        'data-original-title' => $this->trans('Edit', 'Admin.Actions'),
                    ],
                ],
            ],
            'delete' => [
                'type' => IconButtonType::class,
                'options' => [
                    'label' => $this->trans('Delete', 'Admin.Actions'),
                    'icon' => 'delete',
                    'attr' => [
                        'class' => 'delete-combination-item tooltip-link',
                        'data-modal-title' => $this->trans('Delete item', 'Admin.Notifications.Warning'),
                        'data-modal-message' => $deleteItemMessage,
                        'data-modal-apply' => $this->trans('Delete', 'Admin.Actions'),
                        'data-modal-cancel' => $this->trans('Cancel', 'Admin.Actions'),
                        'data-toggle' => 'pstooltip',
                        'data-original-title' => $this->trans('Delete', 'Admin.Actions'),
                        'data-shop-id' => $this->contextShopId,
                    ],
                ],
            ],
        ];

        if ($this->multiStoreFeature->isActive()) {
            $actionButtons['delete_for_all_shops'] = [
                'type' => IconButtonType::class,
                'options' => [
                    'label' => $this->trans('Delete from all stores', 'Admin.Actions'),
                    'icon' => 'delete',
                    'attr' => [
                        'class' => 'delete-combination-all-shops tooltip-link',
                        'data-modal-title' => $this->trans('Delete item', 'Admin.Notifications.Warning'),
                        'data-modal-message' => $this->trans('Delete selected item from all stores?', 'Admin.Notifications.Warning'),
                        'data-modal-apply' => $this->trans('Delete', 'Admin.Actions'),
                        'data-modal-cancel' => $this->trans('Cancel', 'Admin.Actions'),
                        'data-toggle' => 'pstooltip',
                        'data-original-title' => $this->trans('Delete from all stores', 'Admin.Actions'),
                    ],
                ],
            ];
        }

        $builder
            ->add('is_selected', CheckboxType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'combination-is-selected',
                    // Force placeholder in value so that the JS replaces it at the same time as the combination_id field
                    'value' => '__combination_id__',
                ],
            ])
            ->add('image_url', ImagePreviewType::class, [
                'label' => false,
                'image_class' => 'combination-image',
            ])
            ->add('combination_id', TextPreviewType::class, [
                'attr' => [
                    'class' => 'combination-id-input',
                    'data-order-by' => 'id_product_attribute',
                ],
                'label' => $this->trans('ID', 'Admin.Global'),
            ])
            ->add('name', TextPreviewType::class, [
                'label' => $this->trans('Combination', 'Admin.Global'),
                'attr' => [
                    'preview_class' => 'combination-name-preview',
                ],
            ])
            ->add('reference', TextType::class, [
                'constraints' => [
                    new Length([
                        'max' => Reference::MAX_LENGTH,
                        'maxMessage' => $this->trans(
                            'The %1$s field is too long (%2$d chars max).',
                            'Admin.Notifications.Error',
                            ['%1$s' => 'reference', '%2$d' => Reference::MAX_LENGTH]
                        ),
                    ]),
                    new TypedRegex(TypedRegex::TYPE_REFERENCE),
                ],
                'attr' => [
                    'class' => 'combination-reference',
                ],
                'label' => $this->trans('Reference', 'Admin.Global'),
                'empty_data' => '',
            ])
            ->add('impact_on_price_te', MoneyType::class, [
                'attr' => [
                    'class' => 'combination-impact-on-price-tax-excluded',
                    'data-order-by' => 'price',
                ],
                'label' => $this->trans('Impact on price (tax excl.)', 'Admin.Catalog.Feature'),
                'currency' => $this->defaultCurrency->iso_code,
            ])
            ->add('impact_on_price_ti', MoneyType::class, [
                'attr' => [
                    'class' => 'combination-impact-on-price-tax-included',
                    'data-order-by' => 'price',
                ],
                'label' => $this->trans('Impact on price (tax incl.)', 'Admin.Catalog.Feature'),
                'currency' => $this->defaultCurrency->iso_code,
            ])
            ->add('eco_tax', HiddenType::class, [
                'attr' => [
                    'class' => 'combination-eco-tax',
                ],
            ])
            ->add('final_price_te', TextPreviewType::class, [
                'attr' => [
                    'class' => 'combination-final-price',
                    'data-order-by' => 'price',
                ],
                'label' => $this->trans('Final price (tax excl.)', 'Admin.Catalog.Feature'),
            ])
            ->add('delta_quantity', DeltaQuantityType::class, [
                'attr' => [
                    'data-order-by' => 'quantity',
                ],
                'delta_label' => false,
                'label' => $this->trans('Quantity', 'Admin.Global'),
            ])
            ->add('is_default', RadioType::class, [
                'label' => $this->trans('Default combination', 'Admin.Catalog.Feature'),
                'attr' => [
                    'class' => 'combination-is-default-input',
                ],
            ])
            ->add('actions', ButtonCollectionType::class, [
                'buttons' => $actionButtons,
                'label' => $this->trans('Actions', 'Admin.Global'),
                'attr' => [
                    'class' => 'combination-row-actions',
                ],
                'inline_buttons_limit' => self::INLINE_ACTIONS_LIMIT,
                'use_inline_labels' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        $view->vars['placeholder_data'] = $this->getPlaceholderData($form->all());
    }

    /**
     * @param FormInterface[] $children
     *
     * @return array
     */
    protected function getPlaceholderData(array $children): array
    {
        $data = [];
        foreach ($children as $child) {
            if ($child->count()) {
                $data[$child->getName()] = $this->getPlaceholderData($child->all());
            } else {
                $data[$child->getName()] = '__' . $child->getName() . '__';
            }
        }

        return $data;
    }
}
