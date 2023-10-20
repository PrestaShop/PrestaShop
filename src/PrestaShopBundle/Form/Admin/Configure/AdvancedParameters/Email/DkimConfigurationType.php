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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DkimConfigurationType build form for DKIM data configuration.
 */
class DkimConfigurationType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain', TextType::class, [
                'label' => $this->trans('DKIM domain', 'Admin.Advparameters.Feature'),
                'required' => false,
                'empty_data' => '',
            ])
            ->add('selector', TextType::class, [
                'label' => $this->trans('DKIM selector', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('A DKIM selector usually looks like 12345.domain. It must match the name of your DNS record.', 'Admin.Advparameters.Help'),
                'required' => false,
                'empty_data' => '',
            ])
            ->add('key', TextareaType::class, [
                'label' => $this->trans('DKIM private key', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('The private key starts with -----BEGIN RSA PRIVATE KEY-----.', 'Admin.Advparameters.Help'),
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'rows' => 10,
                ],
            ]);
    }
}
