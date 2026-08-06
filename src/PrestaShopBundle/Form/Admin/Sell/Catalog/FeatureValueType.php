<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Feature\FeatureValueSettings;
use PrestaShopBundle\Form\Admin\Type\FeatureChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class FeatureValueType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('feature_value_id', HiddenType::class)
            ->add('feature_id', FeatureChoiceType::class, [
                'attr' => [
                    // disable feature id choices when editing feature value and only allow changing them when creating new one
                    'disabled' => !empty($builder->getData()['feature_value_id']),
                ],
            ])
            ->add('value', TranslatableType::class, [
                'label' => $this->trans('Value', 'Admin.Global'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new CleanHtml([
                            'message' => $this->trans('%s is invalid.', 'Admin.Notifications.Error'),
                        ]),
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                        new Length(['max' => FeatureValueSettings::VALUE_MAX_LENGTH]),
                    ],
                ],
                'help' => $this->trans('Invalid characters: %chars%', 'Admin.Notifications.Info', ['%chars%' => '<>{}']),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit.html.twig',
        ]);
    }
}
