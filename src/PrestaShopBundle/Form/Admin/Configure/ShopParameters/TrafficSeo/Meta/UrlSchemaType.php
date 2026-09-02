<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShop\PrestaShop\Adapter\Routes\DefaultRouteProvider;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Form\Extension\MultistoreConfigurationTypeExtension;
use PrestaShopException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class UrlSchemaType is responsible for providing form fields for
 * Shop parameters -> Traffic & Seo -> Seo & Urls -> Schema of urls block.
 */
class UrlSchemaType extends TranslatorAwareType
{
    /**
     * @var DefaultRouteProvider
     */
    private $defaultRouteProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        DefaultRouteProvider $defaultRouteProvider
    ) {
        parent::__construct($translator, $locales);
        $this->defaultRouteProvider = $defaultRouteProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product_rule', TranslatableType::class, [
                'label' => $this->trans(
                    'Route to products',
                    'Admin.Shopparameters.Feature'
                ),
                'type' => TextType::class,
                'only_enabled_locales' => false,
                'help' => $this->getKeywords('product_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_product_rule',
                'options' => [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'This field cannot be empty.',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('category_rule', TranslatableType::class, [
                'label' => $this->trans(
                    'Route to category',
                    'Admin.Shopparameters.Feature'
                ),
                'type' => TextType::class,
                'only_enabled_locales' => false,
                'help' => $this->getKeywords('category_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_category_rule',
                'options' => [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'This field cannot be empty.',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('supplier_rule', TranslatableType::class, [
                'label' => $this->trans(
                    'Route to supplier',
                    'Admin.Shopparameters.Feature'
                ),
                'type' => TextType::class,
                'only_enabled_locales' => false,
                'help' => $this->getKeywords('supplier_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_supplier_rule',
                'options' => [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'This field cannot be empty.',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('manufacturer_rule', TranslatableType::class, [
                'label' => $this->trans(
                    'Route to brand',
                    'Admin.Shopparameters.Feature'
                ),
                'type' => TextType::class,
                'only_enabled_locales' => false,
                'help' => $this->getKeywords('manufacturer_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_manufacturer_rule',
                'options' => [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'This field cannot be empty.',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('cms_rule', TranslatableType::class, [
                'label' => $this->trans(
                    'Route to page',
                    'Admin.Shopparameters.Feature'
                ),
                'type' => TextType::class,
                'only_enabled_locales' => false,
                'help' => $this->getKeywords('cms_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_cms_rule',
                'options' => [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'This field cannot be empty.',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('cms_category_rule', TranslatableType::class, [
                'label' => $this->trans(
                    'Route to page category',
                    'Admin.Shopparameters.Feature'
                ),
                'type' => TextType::class,
                'only_enabled_locales' => false,
                'help' => $this->getKeywords('cms_category_rule'),
                'multistore_configuration_key' => 'PS_ROUTE_cms_category_rule',
                'options' => [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'This field cannot be empty.',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('module', TranslatableType::class, [
                'label' => $this->trans(
                    'Route to modules',
                    'Admin.Shopparameters.Feature'
                ),
                'type' => TextType::class,
                'only_enabled_locales' => false,
                'help' => $this->getKeywords('module'),
                'multistore_configuration_key' => 'PS_ROUTE_module',
                'options' => [
                    'required' => true,
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->trans(
                                'This field cannot be empty.',
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'label' => false,
        ]);
    }

    /**
     * @param string $idRoute
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    private function getKeywords($idRoute)
    {
        $keyWords = $this->defaultRouteProvider->getKeywords();
        $formattedKeyWords = [];
        if ($keyWords[$idRoute]) {
            foreach ($keyWords[$idRoute] as $key => $keyWord) {
                $value = $key;
                if (isset($keyWord['param'])) {
                    $value .= '*';
                }
                $formattedKeyWords[] = $value;
            }
        }

        return $this->trans(
            'Keywords: %keywords%',
            'Admin.Shopparameters.Feature',
            [
                '%keywords%' => implode(', ', $formattedKeyWords),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * @see MultistoreConfigurationTypeExtension
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
