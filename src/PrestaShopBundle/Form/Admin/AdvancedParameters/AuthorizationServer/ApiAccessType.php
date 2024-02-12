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

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\AuthorizationServer;

use PrestaShop\PrestaShop\Core\Domain\ApiAccess\ApiAccessSettings;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ApiAccessType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('client_name', TextType::class, [
                'label' => $this->trans('Client Name', 'Admin.Advparameters.Feature'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => ApiAccessSettings::MAX_CLIENT_NAME_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters.',
                            'Admin.Notifications.Error',
                            [
                                '%limit%' => ApiAccessSettings::MAX_CLIENT_NAME_LENGTH,
                            ]
                        ),
                    ]),
                ],
            ])
            ->add('client_id', TextType::class, [
                'label' => $this->trans('Client ID', 'Admin.Advparameters.Feature'),
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => ApiAccessSettings::MAX_CLIENT_ID_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters.',
                            'Admin.Notifications.Error',
                            [
                                '%limit%' => ApiAccessSettings::MAX_CLIENT_ID_LENGTH,
                            ]
                        ),
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new Length([
                        'max' => ApiAccessSettings::MAX_DESCRIPTION_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters.',
                            'Admin.Notifications.Error',
                            [
                                '%limit%' => ApiAccessSettings::MAX_DESCRIPTION_LENGTH,
                            ]
                        ),
                    ]),
                ],
            ])
            ->add('lifetime', IntegerType::class, [
                'label' => $this->trans('Lifetime', 'Admin.Global'),
                'required' => false,
                'empty_data' => '3600',
                'constraints' => [
                    new NotBlank(),
                    new Positive(),
                ],
            ])
            ->add('enabled', SwitchType::class, [
                'label' => $this->trans('Enabled', 'Admin.Global'),
                'required' => true,
            ])
            ->add('scopes', ResourceScopesType::class, [
                'label' => $this->trans('Scopes', 'Admin.Advparameters.Feature'),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/Configure/AdvancedParameters/AuthorizationServer/ApiAccess/form_theme.html.twig',
        ]);
    }
}
