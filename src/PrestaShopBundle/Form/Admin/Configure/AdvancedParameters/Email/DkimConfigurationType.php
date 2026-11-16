<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email;

use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class DkimConfigurationType build form for DKIM data configuration.
 */
class DkimConfigurationType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domain', TextType::class, [
                'label' => $this->trans('DKIM domain', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_MAIL_DKIM_DOMAIN',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('selector', TextType::class, [
                'label' => $this->trans('DKIM selector', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_MAIL_DKIM_SELECTOR',
                'help' => $this->trans('A DKIM selector usually looks like 12345.domain. It must match the name of your DNS record.', 'Admin.Advparameters.Help'),
                'required' => false,
                'empty_data' => '',
            ])
            ->add('key', TextareaType::class, [
                'label' => $this->trans('DKIM private key', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_MAIL_DKIM_KEY',
                'help' => $this->trans('The private key starts with -----BEGIN RSA PRIVATE KEY-----.', 'Admin.Advparameters.Help'),
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'rows' => 10,
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
