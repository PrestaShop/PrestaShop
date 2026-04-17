<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Improve\International\Tax;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Form type for tax add/edit
 */
class TaxRulesGroupType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    protected $isShopFeatureEnabled;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isShopFeatureEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        bool $isShopFeatureEnabled
    ) {
        parent::__construct($translator, $locales);
        $this->isShopFeatureEnabled = $isShopFeatureEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => sprintf(
                    '%s %s',
                    $this->trans('Invalid characters:', 'Admin.Notifications.Info'),
                    TypedRegexValidator::CATALOG_CHARS
                ),
                'constraints' => [
                    new Length([
                        'max' => 64,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters.',
                            'Admin.Notifications.Error',
                            ['%limit%' => 64]
                        ),
                    ]),
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_CATALOG_NAME,
                    ]),
                ],
            ])
            ->add('is_enabled', SwitchType::class, [
                'label' => $this->trans('Enable', 'Admin.Actions'),
                'required' => false,
            ])
        ;
        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->trans('Store association', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('Store association', 'Admin.Global')),
                            ]
                        ),
                    ]),
                ],
            ]);
        }
    }
}
