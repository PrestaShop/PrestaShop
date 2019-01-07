<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class SmtpConfigurationType build form for SMTP data configuration.
 */
class SmtpConfigurationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain', TextType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->add('server', TextType::class, [
                'required' => false,
            ])
            ->add('username', TextType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->add('password', PasswordType::class, [
                'required' => false,
                'empty_data' => '',
            ])
            ->add('encryption', ChoiceType::class, [
                'choices' => [
                    'None' => 'off',
                    'TLS' => 'tls',
                    'SSL' => 'ssl',
                ],
                'choice_translation_domain' => 'Admin.Advparameters.Feature',
            ])
            ->add('port', TextType::class, [
                'required' => false,
            ])
        ;
    }
}
