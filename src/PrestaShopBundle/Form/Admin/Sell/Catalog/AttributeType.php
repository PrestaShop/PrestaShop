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

namespace PrestaShopBundle\Form\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Adapter\AttributeGroup\AttributeGroupDataProvider;
use PrestaShop\PrestaShop\Adapter\AttributeGroup\Repository\AttributeGroupRepository;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupId;
use PrestaShop\PrestaShop\Core\Domain\AttributeGroup\ValueObject\AttributeGroupType;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for attribute add/edit
 */
class AttributeType extends TranslatorAwareType
{
    /**
     * @var AttributeGroupRepository
     */
    private $attributeGroupRepository;

    /**
     * @var AttributeGroupDataProvider
     */
    private $attributeGroupDataProvider;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isMultistoreEnabled
     * @param AttributeGroupRepository $attributeGroupRepository
     * @param int $contextShopId
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        AttributeGroupRepository $attributeGroupRepository,
        AttributeGroupDataProvider $attributeGroupDataProvider,
        int $contextShopId,
        int $contextLangId
    ) {
        parent::__construct($translator, $locales);

        $this->attributeGroupRepository = $attributeGroupRepository;
        $this->contextShopId = $contextShopId;
        $this->attributeGroupDataProvider = $attributeGroupDataProvider;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $attributeGroupId = $options['data']['attribute_group'];
        $attributeGroup = $this->attributeGroupRepository->get(
            new AttributeGroupId($attributeGroupId),
            new ShopId($this->contextShopId)
        );
        $builder
            ->add('attribute_group', ChoiceType::class, [
                'label' => $this->trans('Attribute group', 'Admin.Catalog.Feature'),
                'help' => $this->trans('The way the attribute\'s values will be presented to the customers in the product\'s page.', 'Admin.Catalog.Help'),
                'choices' => $this->attributeGroupDataProvider->getAttributeGroupChoices(
                    new ShopId($this->contextShopId),
                    new LanguageId($this->contextLangId)
                ),
            ])
            ->add('value', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Value', 'Admin.Global'),
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => TypedRegex::TYPE_CATALOG_NAME,
                        ]),
                    ],
                ],
                'help' => $this->trans('Your internal name for this attribute.', 'Admin.Catalog.Help')
                    . '&nbsp;' . $this->trans('Invalid characters:', 'Admin.Notifications.Info')
                    . ' ' . TypedRegexValidator::CATALOG_CHARS,
            ]);
        if ($attributeGroup->group_type === AttributeGroupType::ATTRIBUTE_GROUP_TYPE_COLOR) {
            $builder->add('color', ColorType::class, [
                'label' => $this->trans('Color', 'Admin.Global'),
                'required' => false,
            ])->add('texture', FileType::class, [
                'label' => $this->trans('Texture', 'Admin.Global'),
                'required' => false,
            ]);
        }

        $builder->add('shop_association', ShopChoiceTreeType::class, [
            'label' => $this->trans('Shop association', 'Admin.Global'),
            'required' => false,
            'constraints' => [
                new NotBlank([
                    'message' => $this->trans(
                        'This field cannot be empty.', 'Admin.Notifications.Error'
                    ),
                ]),
            ],
        ]);
    }
}
