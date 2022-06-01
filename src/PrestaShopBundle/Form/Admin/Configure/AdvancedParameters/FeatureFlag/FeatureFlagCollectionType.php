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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\FeatureFlag;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Represents the form used to manage feature flags state.
 * There is one SwitchType per existing feature flag.
 */
class FeatureFlagCollectionType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    protected $isMultiShopUsed;

    /**
     * @var FormCloner
     */
    protected $formCloner;

    /**
     * FeatureFlagCollectionType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isMultiShopUsed
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $isMultiShopUsed,
        FormCloner $formCloner
    ) {
        parent::__construct($translator, $locales);
        $this->isMultiShopUsed = $isMultiShopUsed;
        $this->formCloner = $formCloner;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feature_flags', CollectionType::class, [
                'entry_type' => FeatureFlagType::class,
                'label' => false,
                'required' => false,
                'allow_extra_fields' => true,
                'attr' => [
                    'data-is-multi-shop-used' => $this->isMultiShopUsed ? '1' : '0',
                ],
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'addSubmitButton']);
    }

    public function addSubmitButton(PreSetDataEvent $event)
    {
        $featureFlagData = $event->getData();
        $attributes = [];
        if ($featureFlagData['submit']['stability'] === 'beta') {
            $attributes = [
                'data-modal-title' => $this->trans('Are you sure you want to enable this experimental feature?', 'Admin.Advparameters.Notification'),
                'data-modal-message' => $this->trans('You are about to enable a feature that is not stable yet. This should only be done in a test environment or in full knowledge of the potential risks.', 'Admin.Advparameters.Notification'),
                'data-modal-apply' => $this->trans('Enable', 'Admin.Actions'),
                'data-modal-cancel' => $this->trans('Cancel', 'Admin.Actions'),
            ];
        }
        $attributes['disabled'] = $featureFlagData['submit']['disabled'];
        $form = $event->getForm();
        $form->add('submit', SubmitType::class, [
            'label' => $this->trans('Save', 'Admin.Actions'),
            'attr' => $attributes,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => false,
            ])
        ;
    }
}
