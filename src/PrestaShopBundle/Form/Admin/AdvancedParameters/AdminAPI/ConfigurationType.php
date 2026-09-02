<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\AdminAPI;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class ConfigurationType extends TranslatorAwareType
{
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        private readonly bool $isDebug,
    ) {
        parent::__construct($translator, $locales);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $enableAdminAPIHelp = $this->trans(
            'Before enabling the Admin API, you must be sure to:',
            'Admin.Advparameters.Help'
        );
        $enableAdminAPIHelp .= '<br/> 1. ';
        $enableAdminAPIHelp .= $this->trans(
            'Check that the ps_apiressources module is installed and enabled.',
            'Admin.Advparameters.Help'
        );
        $enableAdminAPIHelp .= '<br/> 2. ';
        $enableAdminAPIHelp .= $this->trans(
            'Check that URL rewriting is available on this server.',
            'Admin.Advparameters.Help'
        );
        $enableAdminAPIHelp .= '<br/> 3. ';
        $enableAdminAPIHelp .= $this->trans(
            'Check that the six methods GET, POST, PUT, PATCH, DELETE and HEAD are supported by this server.',
            'Admin.Advparameters.Help'
        );
        $enableAdminAPIHelp .= '<br/> 4. ';
        $enableAdminAPIHelp .= $this->trans(
            'Check that your Admin API endpoint uses HTTPS protocol.',
            'Admin.Advparameters.Help'
        );

        $builder
            ->add('enable_admin_api', SwitchType::class, [
                'label' => $this->trans('Admin API', 'Admin.Advparameters.Feature'),
                'help' => $enableAdminAPIHelp,
                'required' => false,
                'choices' => [
                    'Disabled' => false,
                    'Enabled' => true,
                ],
            ])
            ->add('force_debug_secured', SwitchType::class, [
                'label' => $this->trans('Force security in debug mode', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Admin API forces the use of HTTPS with TLSv1.2 or higher, you can disable this check for debug mode only.', 'Admin.Advparameters.Help'),
                'required' => false,
                'choices' => [
                    'Disabled' => false,
                    'Enabled' => true,
                ],
                'disabled' => !$this->isDebug,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/TwigTemplateForm/prestashop_ui_kit.html.twig',
        ]);
    }
}
