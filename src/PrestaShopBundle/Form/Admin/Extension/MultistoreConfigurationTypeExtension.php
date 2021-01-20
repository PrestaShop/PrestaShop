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
use PrestaShopBundle\Service\Form\MultistoreCheckboxAttacher;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class MultistoreConfigurationTypeExtension extends AbstractTypeExtension
{
    /**
     * @var MultistoreCheckboxAttacher
     */
    private $multistoreCheckboxAttacher;

    public function __construct(MultistoreCheckboxAttacher $multistoreCheckboxAttacher)
    {
        $this->multistoreCheckboxAttacher = $multistoreCheckboxAttacher;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if (!$this->multistoreCheckboxAttacher->shouldAddCheckboxes()) {
            return;
        }

        $checkboxAttacher = $this->multistoreCheckboxAttacher;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($checkboxAttacher) {
            $form = $event->getForm();
            $checkboxAttacher->addCheckboxes($form);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType(): string
    {
        return MultistoreConfigurationType::class;
    }
}
