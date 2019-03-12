<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\Design\Theme;

use PrestaShop\PrestaShop\Core\Form\ValueObject\ShopRestriction;
use PrestaShop\PrestaShop\Core\Form\ValueObject\ShopRestrictionField;
use PrestaShopBundle\Form\Admin\Type\ShopRestrictionCheckboxType;
use PrestaShopBundle\Form\Admin\Type\ShopRestrictionSwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
        $builder
            ->add('header_logo', FileType::class, [
                'required' => false,
                'attr' => [
                    'data-test' => 1,
                ],
            ])
            ->add('mail_logo', FileType::class, [
                'required' => false,
            ])
            ->add('invoice_logo', FileType::class, [
                'required' => false,
            ])
            ->add('favicon', FileType::class, [
                'required' => false,
            ])
        ;

        $this->appendWithMultiShopFormFields($builder);
    }

    /**
     * It created additional ShopRestrictionType fields for all existing form fields
     * which are used to restrict certain configuration for specific shop only. It also has data transformer
     * which helps to map all the fields so the post is aware of the fields which are being modified for specific shop.
     * And it also disabled the fields which are not checked.
     *
     * @param FormBuilderInterface $builder
     */
    private function appendWithMultiShopFormFields(FormBuilderInterface $builder)
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

        $builder->add('shop_restriction_switch', ShopRestrictionSwitchType::class, [
            'attr' => [
                'is_allowed_to_display' => $isAllowedToDisplay,
            ],
        ]);

        if ($isAllowedToDisplay) {
            $this->transformMultiStoreFields($builder, $suffix);
            $this->disableAllShopContextFields($builder, $suffix);
            $this->setShopRestrictionSource($builder, $suffix);
        }
    }

    /**
     * When form is submitted it adds extra form field called shop_restriction which is an object which holds
     * for which fields the checkbox has been clicked.
     *
     * @param FormBuilderInterface $builder
     * @param string $suffix - helps to find multi shop checkbox field.
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
     * @param string $suffix - helps to find multi shop checkbox field.
     */
    private function disableAllShopContextFields(FormBuilderInterface $builder, $suffix)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($suffix) {
            $form = $event->getForm();
            $data = $event->getData();

            foreach ($data as $fieldName => $value) {
                if ($value || !$this->stringEndsWith($fieldName, $suffix)) {
                    continue;
                }

                $originalFieldName = $this->getOriginalFieldNameFromSuffix($fieldName, $suffix);

                $formField = $form->get($originalFieldName);
                $formType = $formField->getConfig()->getType()->getInnerType();
                $options = $formField->getConfig()->getOptions();
                $options['disabled'] = true;
                $form->add($originalFieldName, get_class($formType), $options);
            }
        });
    }

    /**
     * Sets the source attribute fields so they can be mapped with the shop restriction checkbox fields later on in
     * javascript events.
     *
     * @param FormBuilderInterface $builder
     * @param string $suffix - helps to find multi shop checkbox field.
     */
    private function setShopRestrictionSource(FormBuilderInterface $builder, $suffix)
    {
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($suffix) {
            $form = $event->getForm();

            foreach ($form as $formField) {
                $fieldName = $formField->getName();

                if (!$this->stringEndsWith($fieldName, $suffix)) {
                    $formType = $formField->getConfig()->getType()->getInnerType();
                    $options = $formField->getConfig()->getOptions();
                    $options['attr']['data-shop-restriction-source'] = $fieldName;

                    $form->add($fieldName, get_class($formType), $options);
                }
            }
        });
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
