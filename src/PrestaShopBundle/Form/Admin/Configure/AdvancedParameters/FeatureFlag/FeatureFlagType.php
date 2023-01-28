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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class FeatureFlagType extends TranslatorAwareType
{
    /**
     * @var FormCloner
     */
    protected $formCloner;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormCloner $formCloner
    ) {
        parent::__construct($translator, $locales);
        $this->formCloner = $formCloner;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', SwitchType::class, [
                'choices' => [
                    $this->trans('Disabled', 'Admin.Global') => false,
                    $this->trans('Enabled', 'Admin.Global') => true,
                ],
                'required' => false,
            ])
            ->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'adaptSwitchOption'])
        ;
    }

    public function adaptSwitchOption(PreSetDataEvent $event): void
    {
        $featureFlagData = $event->getData();
        $form = $event->getForm();
        $form->add($this->formCloner->cloneForm($form->get('enabled'), [
            'label' => $this->trans($featureFlagData['label'], $featureFlagData['label_domain']),
            'help' => $this->trans($featureFlagData['description'], $featureFlagData['description_domain']),
            'attr' => ['disabled' => $featureFlagData['disabled']],
        ]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver
            ->setDefaults([
                'label' => false,
                'required' => false,
            ])
        ;
    }
}
