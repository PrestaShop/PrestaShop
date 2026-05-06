<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * "Customer service options" IMAP panel powering the inbox synchronization.
 */
final class ImapOptionsType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('imap_url', TextType::class, [
                'label' => $this->trans('IMAP URL', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'URL for your IMAP server (ie.: mail.server.com).',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ])
            ->add('imap_port', TextType::class, [
                'label' => $this->trans('IMAP port', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'Port to use to connect to your IMAP server.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ])
            ->add('imap_user', TextType::class, [
                'label' => $this->trans('IMAP user', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'User to use to connect to your IMAP server.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ])
            ->add('imap_password', PasswordType::class, [
                'label' => $this->trans('IMAP password', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'Password to use to connect your IMAP server.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
                'always_empty' => false,
            ])
            ->add('imap_delete_msg', SwitchType::class, [
                'label' => $this->trans('Delete messages', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'Delete messages after synchronization. If you do not enable this option, the synchronization will take more time.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ])
            ->add('imap_create_threads', SwitchType::class, [
                'label' => $this->trans('Create new threads', 'Admin.Catalog.Feature'),
                'help' => $this->trans(
                    'Create new threads for unrecognized emails.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ])
            ->add('imap_opt_pop3', SwitchType::class, [
                'label' => $this->trans('IMAP options', 'Admin.Catalog.Feature') . ' (/pop3)',
                'help' => $this->trans('Use POP3 instead of IMAP.', 'Admin.Catalog.Help'),
                'required' => false,
            ])
            ->add('imap_opt_norsh', SwitchType::class, [
                'label' => $this->trans('IMAP options', 'Admin.Catalog.Feature') . ' (/norsh)',
                'help' => $this->trans(
                    'Do not use RSH or SSH to establish a preauthenticated IMAP sessions.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ])
            ->add('imap_opt_ssl', SwitchType::class, [
                'label' => $this->trans('IMAP options', 'Admin.Catalog.Feature') . ' (/ssl)',
                'help' => $this->trans(
                    'Use the Secure Socket Layer (TLS/SSL) to encrypt the session.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ])
            ->add('imap_opt_validate_cert', SwitchType::class, [
                'label' => $this->trans('IMAP options', 'Admin.Catalog.Feature') . ' (/validate-cert)',
                'help' => $this->trans('Validate certificates from the TLS/SSL server.', 'Admin.Catalog.Help'),
                'required' => false,
            ])
            ->add('imap_opt_novalidate_cert', SwitchType::class, [
                'label' => $this->trans('IMAP options', 'Admin.Catalog.Feature') . ' (/novalidate-cert)',
                'help' => $this->trans(
                    'Do not validate certificates from the TLS/SSL server. This is only needed if a server uses self-signed certificates.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ])
            ->add('imap_opt_tls', SwitchType::class, [
                'label' => $this->trans('IMAP options', 'Admin.Catalog.Feature') . ' (/tls)',
                'help' => $this->trans(
                    'Force use of start-TLS to encrypt the session, and reject connection to servers that do not support it.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ])
            ->add('imap_opt_notls', SwitchType::class, [
                'label' => $this->trans('IMAP options', 'Admin.Catalog.Feature') . ' (/notls)',
                'help' => $this->trans(
                    'Do not use start-TLS to encrypt the session, even with servers that support it.',
                    'Admin.Catalog.Help'
                ),
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Catalog.Feature',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'imap_options_block';
    }
}
