<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email;

use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class TestEmailSendingType is responsible for building form type used to send testing emails.
 */
class TestEmailSendingType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('send_email_to', EmailType::class, [
                'label' => $this->trans('Send a test email to', 'Admin.Advparameters.Feature'),
            ])
            ->add('mail_method', HiddenType::class)
            ->add('smtp_server', HiddenType::class)
            ->add('smtp_username', HiddenType::class)
            ->add('smtp_password', HiddenType::class)
            ->add('smtp_port', HiddenType::class)
            ->add('smtp_encryption', HiddenType::class)
            ->add('dkim_enable', HiddenType::class)
            ->add('dkim_key', HiddenType::class)
            ->add('dkim_domain', HiddenType::class)
            ->add('dkim_selector', HiddenType::class);
    }
}
