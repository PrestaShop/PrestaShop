<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Options;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\CustomizationFieldSettings;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\IconButtonType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class CustomizationFieldType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $customizationFieldTypeChoiceProvider;

    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $customizationFieldTypeChoiceProvider
    ) {
        parent::__construct($translator, $locales);
        $this->customizationFieldTypeChoiceProvider = $customizationFieldTypeChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Label', 'Admin.Global'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => CustomizationFieldSettings::MAX_NAME_LENGTH,
                        ]),
                    ],
                ],
            ])
            ->add('type', ChoiceType::class, [
                'choices' => $this->customizationFieldTypeChoiceProvider->getChoices(),
            ])
            ->add('required', SwitchType::class, [
                'label' => $this->trans('Required', 'Admin.Global'),
                'choices' => [
                    $this->trans('Not Required', 'Admin.Global') => false,
                    $this->trans('Required', 'Admin.Global') => true,
                ],
                'default_empty_data' => false,
            ])
            ->add('remove', IconButtonType::class, [
                'icon' => 'delete',
                'attr' => [
                    'class' => 'text-secondary remove-customization-btn tooltip-link',
                    'data-modal-title' => $this->trans('Delete item', 'Admin.Notifications.Warning'),
                    'data-modal-message' => $this->trans('Are you sure you want to delete this item?', 'Admin.Notifications.Warning'),
                    'data-modal-apply' => $this->trans('Delete', 'Admin.Actions'),
                    'data-modal-cancel' => $this->trans('Cancel', 'Admin.Actions'),
                    'data-toggle' => 'pstooltip',
                    'data-original-title' => $this->trans('Delete', 'Admin.Global'),
                ],
            ])
        ;
    }
}
