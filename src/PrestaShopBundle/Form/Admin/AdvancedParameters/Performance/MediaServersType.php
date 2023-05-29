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

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Performance;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This form class generates the "Media servers" form in Performance page.
 */
class MediaServersType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media_server_one', TextType::class, [
                'label' => $this->trans('Media server #1', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Name of the second domain of your shop, (e.g. myshop-media-server-1.com). If you do not have another domain, leave this field blank.', 'Admin.Advparameters.Help'),
                'empty_data' => '',
                'required' => false,
            ])
            ->add('media_server_two', TextType::class, [
                'label' => $this->trans('Media server #2', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Name of the third domain of your shop, (e.g. myshop-media-server-2.com). If you do not have another domain, leave this field blank.', 'Admin.Advparameters.Help'),
                'empty_data' => '',
                'required' => false,
            ])
            ->add('media_server_three', TextType::class, [
                'label' => $this->trans('Media server #3', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Name of the fourth domain of your shop, (e.g. myshop-media-server-3.com). If you do not have another domain, leave this field blank.', 'Admin.Advparameters.Help'),
                'empty_data' => '',
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'performance_media_servers_block';
    }
}
