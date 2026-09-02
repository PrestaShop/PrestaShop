<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\TrafficSeo\Meta;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\IsUrlRewrite;
use PrestaShopBundle\Form\Admin\Type\TextWithRecommendedLengthType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class MetaType is responsible for providing form fields for Shop parameters -> Traffic & Seo ->
 * Seo & Urls -> add and edit forms.
 */
class MetaType extends AbstractType
{
    use TranslatorAwareTrait;

    public const TITLE_MAX_CHARS = 128;
    public const META_DESCRIPTION_MAX_CHARS = 255;
    public const RECOMMENDED_TITLE_LENGTH = 70;
    public const RECOMMENDED_DESCRIPTION_LENGTH = 160;

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
        array $modulePageChoices,
        TranslatorInterface $translator
    ) {
        $this->defaultPageChoices = $defaultPageChoices;
        $this->modulePageChoices = $modulePageChoices;
        $this->translator = $translator;
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
                'type' => TextWithRecommendedLengthType::class,
                'options' => [
                    'recommended_length' => self::RECOMMENDED_TITLE_LENGTH,
                    'attr' => [
                        'maxlength' => self::TITLE_MAX_CHARS,
                    ],
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[^<>{}]*$/u',
                            'message' => $this->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                        new Length([
                            'max' => self::TITLE_MAX_CHARS,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => self::TITLE_MAX_CHARS],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                    'required' => false,
                ],
            ])
            ->add('meta_description', TranslatableType::class, [
                'required' => false,
                'type' => TextWithRecommendedLengthType::class,
                'options' => [
                    'recommended_length' => self::RECOMMENDED_DESCRIPTION_LENGTH,
                    'attr' => [
                        'maxlength' => self::META_DESCRIPTION_MAX_CHARS,
                    ],
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[^<>{}]*$/u',
                            'message' => $this->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                        new Length([
                            'max' => self::META_DESCRIPTION_MAX_CHARS,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => self::META_DESCRIPTION_MAX_CHARS],
                                'Admin.Notifications.Error'
                            ),
                        ]),
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
