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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class MetaType is responsible for providing form fields for Shop parameters -> Traffic & Seo ->
 * Seo & Urls -> add and edit forms.
 */
class MetaType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var array
     */
    private $defaultPageChoices;

    /**
     * @var array
     */
    private $modulePageChoices;

    /**
     * @param array $defaultPageChoices
     * @param array $modulePageChoices
     */
    public function __construct(
        array $defaultPageChoices,
        array $modulePageChoices
    ) {
        $this->defaultPageChoices = $defaultPageChoices;
        $this->modulePageChoices = $modulePageChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('page_name', ChoiceType::class, [
                'choices' => [
                    $this->trans('Default pages', [], 'Admin.Shopparameters.Feature') => $this->defaultPageChoices,
                    $this->trans('Module pages', [], 'Admin.Shopparameters.Feature') => $this->modulePageChoices,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->trans('Page name', [], 'Admin.Shopparameters.Feature')),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z0-9_.-]+$/',
                        'message' => $this->trans(
                            '%s is invalid.',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'choice_translation_domain' => false,
            ])
            ->add('page_title', TranslatableType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[^<>={}]*$/u',
                            'message' => $this->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                    'required' => false,
                ],
            ])
            ->add('meta_description', TranslatableType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[^<>={}]*$/u',
                            'message' => $this->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                    'required' => false,
                ],
            ])
            ->add('meta_keywords', TranslatableType::class, [
                'required' => false,
                'options' => [
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[^<>={}]*$/u',
                            'message' => $this->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                    'attr' => [
                        'class' => 'js-taggable-field',
                        'placeholder' => $this->trans('Add tag', [], 'Admin.Actions'),
                    ],
                    'required' => false,
                ],
            ])
            ->add('url_rewrite', TranslatableType::class, [
                'options' => [
                    'constraints' => [
                        new IsUrlRewrite(),
                    ],
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $formData = $event->getData();

            if (isset($formData['page_name']) && 'index' !== $formData['page_name']) {
                $form = $event->getForm();
                $form->add('url_rewrite', TranslatableType::class, [
                    'constraints' => [
                        new DefaultLanguage(),
                    ],
                    'options' => [
                        'constraints' => [
                            new IsUrlRewrite(),
                        ],
                    ],
                ]);
            }
        });
    }
}
