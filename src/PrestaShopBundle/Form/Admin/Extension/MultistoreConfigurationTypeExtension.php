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

namespace PrestaShopBundle\Form\Admin\Extension;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Service\Form\MultistoreCheckboxEnabler;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;

class MultistoreConfigurationTypeExtension extends AbstractTypeExtension
{
    /**
     * @var MultistoreCheckboxEnabler
     */
    private $multistoreCheckboxEnabler;

    public function __construct(MultistoreCheckboxEnabler $multistoreCheckboxEnabler)
    {
        $this->multistoreCheckboxEnabler = $multistoreCheckboxEnabler;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$this->multistoreCheckboxEnabler->shouldAddMultistoreElements()) {
            return;
        }

        // when we have multistore checkboxes, we send partial data, so we need to use the http PATCH method
        $builder->setMethod(Request::METHOD_PATCH);

        $checkboxEnabler = $this->multistoreCheckboxEnabler;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($checkboxEnabler) {
            $form = $event->getForm();
            $checkboxEnabler->addMultistoreElements($form);
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function getExtendedTypes(): iterable
    {
        return [MultistoreConfigurationType::class];
    }
}
