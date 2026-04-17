<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration;

use PrestaShop\PrestaShop\Core\Http\CookieOptions;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneralType extends TranslatorAwareType
{
    public const FIELD_FRONT_COOKIE_LIFETIME = 'front_cookie_lifetime';
    public const FIELD_BACK_COOKIE_LIFETIME = 'back_cookie_lifetime';
    public const FIELD_COOKIE_SAMESITE = 'cookie_samesite';

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('check_ip_address', SwitchType::class, [
                'label' => $this->trans('Check the cookie\'s IP address', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Check the IP address of the cookie in order to prevent your cookie from being stolen.', 'Admin.Advparameters.Help'),
            ])
            ->add(self::FIELD_FRONT_COOKIE_LIFETIME, IntegerType::class, [
                'label' => $this->trans('Lifetime of front office cookies', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Set the amount of hours during which the front office cookies are valid. After that amount of time, the customer will have to log in again.', 'Admin.Advparameters.Help'),
                'unit' => $this->trans('hours', 'Admin.Shopparameters.Feature'),
            ])
            ->add(self::FIELD_BACK_COOKIE_LIFETIME, IntegerType::class, [
                'label' => $this->trans('Lifetime of back office cookies', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('When you access your back office and decide to stay logged in, your cookies lifetime defines your browser session. Set here the number of hours during which you want them valid before logging in again.', 'Admin.Advparameters.Help'),
                'unit' => $this->trans('hours', 'Admin.Shopparameters.Feature'),
            ])
            ->add(self::FIELD_COOKIE_SAMESITE, ChoiceType::class, [
                'label' => $this->trans('Cookie SameSite', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Allows you to declare if your cookie should be restricted to a first-party or same-site context.', 'Admin.Advparameters.Help'),
                'choices' => CookieOptions::SAMESITE_AVAILABLE_VALUES,
            ]);
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
        return 'administration_general_block';
    }
}
