<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\AdminAPI;

use PrestaShop\PrestaShop\Core\Domain\ApiClient\ApiClientSettings;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TextPreviewType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ApiClientType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // If an external issuer is specified most of the form is not editable or relevant
        $formData = $builder->getData();
        $isExternalApiClient = !empty($formData['external_issuer'] ?? null);

        $builder
            ->add('client_name', TextType::class, [
                'label' => $this->trans('Client Name', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('A friendly name to identify this API client.', 'Admin.Advparameters.Help'),
                'required' => !$isExternalApiClient,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => ApiClientSettings::MAX_CLIENT_NAME_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters.',
                            'Admin.Notifications.Error',
                            [
                                '%limit%' => ApiClientSettings::MAX_CLIENT_NAME_LENGTH,
                            ]
                        ),
                    ]),
                ],
                'disabled' => $isExternalApiClient,
                'attr' => [
                    'class' => 'js-client-id-source',
                ],
            ])
            ->add('client_id', TextType::class, [
                'label' => $this->trans('Client ID', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('The unique identifier for this client, used for API authentication. Only lowercase letters, numbers and hyphens are allowed.', 'Admin.Advparameters.Help'),
                'required' => !$isExternalApiClient,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => ApiClientSettings::MAX_CLIENT_ID_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters.',
                            'Admin.Notifications.Error',
                            [
                                '%limit%' => ApiClientSettings::MAX_CLIENT_ID_LENGTH,
                            ]
                        ),
                    ]),
                ],
                'disabled' => $isExternalApiClient,
                'attr' => [
                    'class' => 'js-client-id-destination',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
                'help' => $this->trans('Optional description to help identify the purpose of this API client.', 'Admin.Advparameters.Help'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new Length([
                        'max' => ApiClientSettings::MAX_DESCRIPTION_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters.',
                            'Admin.Notifications.Error',
                            [
                                '%limit%' => ApiClientSettings::MAX_DESCRIPTION_LENGTH,
                            ]
                        ),
                    ]),
                ],
            ])
        ;

        if (!$isExternalApiClient) {
            $builder
                ->add('lifetime', IntegerType::class, [
                    'label' => $this->trans('Lifetime', 'Admin.Global'),
                    'help' => $this->trans('The duration for which an access token remains valid.', 'Admin.Advparameters.Help'),
                    'unit' => $this->trans('seconds', 'Admin.Advparameters.Feature'),
                    'required' => false,
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
        } else {
            $builder
                ->add('external_issuer', TextPreviewType::class, [
                    'label' => $this->trans('External issuer', 'Admin.Advparameters.Feature'),
                    'required' => false,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/Configure/AdvancedParameters/AdminAPI/ApiClient/form_theme.html.twig',
        ]);
    }
}
