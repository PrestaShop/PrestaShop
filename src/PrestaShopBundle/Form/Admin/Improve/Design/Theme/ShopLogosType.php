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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Theme;

use PrestaShop\PrestaShop\Core\Domain\Shop\DTO\ShopLogoSettings;
use PrestaShop\PrestaShop\Core\Form\DTO\ShopRestriction;
use PrestaShop\PrestaShop\Core\Form\DTO\ShopRestrictionField;
use PrestaShopBundle\Form\Admin\Type\ShopRestrictionCheckboxType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * Class ThemeLogosType is used to configure theme's logos.
 */
class ShopLogosType extends AbstractType
{
    /**
     * @var bool
     */
    private $isShopFeatureUsed;

    /**
     * @var bool
     */
    private $isSingleShopContext;

    /**
     * @var array
     */
    private $contextShopIds;

    /**
     * @param bool $isShopFeatureUsed
     * @param bool $isSingleShopContext
     * @param array $contextShopIds
     */
    public function __construct(
        $isShopFeatureUsed,
        $isSingleShopContext,
        array $contextShopIds
    ) {
        $this->isShopFeatureUsed = $isShopFeatureUsed;
        $this->isSingleShopContext = $isSingleShopContext;
        $this->contextShopIds = $contextShopIds;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $shopLogoSettings = new ShopLogoSettings();

        $builder
            ->add('header_logo', FileType::class, [
                'required' => false,
                'attr' => [
                    'accept' => implode(',', $shopLogoSettings->getLogoImageExtensionsWithDot()),
                ],
            ])
            ->add('mail_logo', FileType::class, [
                'required' => false,
                'attr' => [
                    'accept' => implode(',', $shopLogoSettings->getLogoImageExtensionsWithDot('PS_LOGO_MAIL')),
                ],
            ])
            ->add('invoice_logo', FileType::class, [
                'required' => false,
                'attr' => [
                    'accept' => implode(',', $shopLogoSettings->getLogoImageExtensionsWithDot('PS_LOGO_INVOICE')),
                ],
            ])
            ->add('favicon', FileType::class, [
                'required' => false,
                'attr' => [
                    'accept' => $shopLogoSettings->getIconImageExtensionWithDot(),
                ],
            ])
        ;

        $this->appendWithMultiShopCheckboxFormFields($builder);
        $this->appendWithMultiShopSwitchField($builder);
    }

    /**
     * It created additional ShopRestrictionType fields for all existing form fields
     * which are used to restrict certain configuration for specific shop only. It also has data transformer
     * which helps to map all the fields so the post is aware of the fields which are being modified for specific shop.
     * And it also disabled the fields which are not checked.
     *
     * @param FormBuilderInterface $builder
     */
    private function appendWithMultiShopCheckboxFormFields(FormBuilderInterface $builder)
    {
        // usually checkboxes should be visible in shop group but on this page it only works for single shop context.
        $isAllowedToDisplay = $this->isShopFeatureUsed && $this->isSingleShopContext;

        $suffix = '_is_restricted_to_shop';

        /** @var FormBuilderInterface $form */
        foreach ($builder as $form) {
            $builder->add($form->getName() . $suffix, ShopRestrictionCheckboxType::class, [
                'attr' => [
                    'is_allowed_to_display' => $isAllowedToDisplay,
                    'data-shop-restriction-target' => $form->getName(),
                ],
            ]);
        }

        if ($isAllowedToDisplay) {
            $this->transformMultiStoreFields($builder, $suffix);
            $this->disableAllShopContextFields($builder, $suffix);
            $this->setShopRestrictionSource($builder, $suffix);
        }
    }

    /**
     * adds switch field to current form which toggles all multi-shop checkboxes on or off.
     *
     * @param FormBuilderInterface $builder
     */
    private function appendWithMultiShopSwitchField(FormBuilderInterface $builder)
    {
        $isAllowedToDisplay = $this->isShopFeatureUsed && $this->isSingleShopContext;

        if ($isAllowedToDisplay) {
            $builder->add('shop_restriction_switch', SwitchType::class, [
                'data' => false,
                'required' => false,
                'attr' => [
                    'data-target-form-name' => $builder->getName(),
                ],
            ]);
        }
    }

    /**
     * When form is submitted it adds extra form field called shop_restriction which is an object which holds
     * for which fields the checkbox has been clicked.
     *
     * @param FormBuilderInterface $builder
     * @param string $suffix - helps to find multi shop checkbox field
     */
    private function transformMultiStoreFields(FormBuilderInterface $builder, $suffix)
    {
        $builder->addModelTransformer(new CallbackTransformer(
            function ($form) {
                return $form;
            },
            function ($form) use ($suffix) {
                $restrictedToShopFields = [];
                foreach ($form as $fieldName => $value) {
                    $isShopRestrictionField = $this->stringEndsWith($fieldName, $suffix);

                    if ($isShopRestrictionField) {
                        $restrictedToShopFields[] = new ShopRestrictionField(
                            $this->getOriginalFieldNameFromSuffix($fieldName, $suffix),
                            $value
                        );
                    }
                }

                $form['shop_restriction'] = new ShopRestriction(
                    $this->contextShopIds,
                    $restrictedToShopFields
                );

                return $form;
            }
        ));
    }

    /**
     * The fields which does not have checked checkbox are being disabled by default
     *
     * @param FormBuilderInterface $builder
     * @param string $suffix - helps to find multi shop checkbox field
     */
    private function disableAllShopContextFields(FormBuilderInterface $builder, $suffix)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($suffix) {
            $form = $event->getForm();

            if ($form->isSubmitted()) {
                return;
            }

            $data = $event->getData();

            foreach ($data as $fieldName => $value) {
                if ($value || !$this->stringEndsWith($fieldName, $suffix)) {
                    continue;
                }

                $originalFieldName = $this->getOriginalFieldNameFromSuffix($fieldName, $suffix);

                $formField = $form->get($originalFieldName);
                $formType = $formField->getConfig()->getType()->getInnerType();
                $options = $formField->getConfig()->getOptions();
                $options['attr']['disabled'] = true;
                $form->add($originalFieldName, get_class($formType), $options);
            }
        });
    }

    /**
     * Sets the source attribute fields so they can be mapped with the shop restriction checkbox fields later on in
     * javascript events.
     *
     * @param FormBuilderInterface $builder
     * @param string $suffix - helps to find multi shop checkbox field
     */
    private function setShopRestrictionSource(FormBuilderInterface $builder, $suffix)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($suffix) {
            $form = $event->getForm();

            if ($form->isSubmitted()) {
                return;
            }

            $sourceFields = $this->getShopRestrictionSourceFormFields($form, $suffix);

            foreach ($sourceFields as $formField) {
                $fieldName = $formField->getName();

                $formType = $formField->getConfig()->getType()->getInnerType();
                $options = $formField->getConfig()->getOptions();
                $options['attr']['data-shop-restriction-source'] = $fieldName;

                $form->add($fieldName, get_class($formType), $options);
            }
        });
    }

    /**
     * Gets the checkbox form fields which are the source of multi-store behavior.
     *
     * @param FormInterface $form
     * @param string $suffix
     *
     * @return array
     */
    private function getShopRestrictionSourceFormFields($form, $suffix)
    {
        $formFields = [];

        foreach ($form as $formField) {
            if (!$this->stringEndsWith($formField->getName(), $suffix)) {
                $formFields[] = $formField;
            }
        }

        return $formFields;
    }

    /**
     * Checks if string ends with certain string.
     *
     * @param string $haystack - the string in which search operation will be performed
     * @param string $needle - the string which is being searched if exists at the end of the string
     *
     * @return bool
     */
    private function stringEndsWith($haystack, $needle)
    {
        $diff = \strlen($haystack) - \strlen($needle);

        return $diff >= 0 && strpos($haystack, $needle, $diff) !== false;
    }

    /**
     * Gets the original field name. E.g if $shopRestrictionFieldName is header_logo_is_restricted_to_shop and
     *  suffix is _is_restricted_to_shop then it will return header_logo
     *
     * @param string $shopRestrictionFieldName
     * @param string $suffix
     *
     * @return string
     */
    private function getOriginalFieldNameFromSuffix($shopRestrictionFieldName, $suffix)
    {
        return str_replace($suffix, '', $shopRestrictionFieldName);
    }
}
