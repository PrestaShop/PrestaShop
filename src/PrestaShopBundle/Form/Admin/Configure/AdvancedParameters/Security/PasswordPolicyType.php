<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Security;

use PrestaShop\PrestaShop\Core\Security\PasswordPolicyConfiguration;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordPolicyType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'minimum_length',
                IntegerType::class,
                [
                    'label' => $this->trans('Minimum length', 'Admin.Advparameters.Feature'),
                    'attr' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'maximum_length',
                IntegerType::class,
                [
                    'label' => $this->trans('Maximum length', 'Admin.Advparameters.Feature'),
                    'attr' => [
                        'min' => 1,
                        'max' => 72,
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'minimum_score',
                ChoiceType::class,
                [
                    'label' => $this->trans('Minimum password security score', 'Admin.Advparameters.Feature'),
                    'choices' => [
                        '0 - Extremely guessable' => PasswordPolicyConfiguration::PASSWORD_EXTREMELY_GUESSABLE,
                        '1 - Very guessable' => PasswordPolicyConfiguration::PASSWORD_VERY_GUESSABLE,
                        '2 - Somewhat guessable' => PasswordPolicyConfiguration::PASSWORD_SOMEWHAT_GUESSABLE,
                        '3 - Safely unguessable' => PasswordPolicyConfiguration::PASSWORD_SAFELY_UNGUESSABLE,
                        '4 - Very unguessable' => PasswordPolicyConfiguration::PASSWORD_VERY_UNGUESSABLE,
                    ],
                    'choice_translation_domain' => 'Admin.Advparameters.Feature',
                    'required' => true,
                ]
            );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Advparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'security_password_policy_block';
    }
}
