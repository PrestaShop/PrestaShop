<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email;

use PrestaShop\PrestaShop\Core\Email\MailOption;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\MultistoreConfigurationType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class EmailConfigurationType defines email sending configuration.
 */
class EmailConfigurationType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $mailMethodChoiceProvider;

    /**
     * @var FormChoiceProviderInterface
     */
    private $contactsChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $mailMethodChoiceProvider
     * @param FormChoiceProviderInterface $contactsChoiceProvider
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $mailMethodChoiceProvider,
        FormChoiceProviderInterface $contactsChoiceProvider
    ) {
        parent::__construct($translator, $locales);

        $this->mailMethodChoiceProvider = $mailMethodChoiceProvider;
        $this->contactsChoiceProvider = $contactsChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('send_emails_to', ChoiceType::class, [
                'label' => $this->trans('Send emails to', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Where customers send messages from the order page.', 'Admin.Advparameters.Help'),
                'choices' => $this->contactsChoiceProvider->getChoices(),
                'choice_translation_domain' => false,
                'multistore_configuration_key' => 'PS_MAIL_EMAIL_MESSAGE',
            ])
            ->add('mail_method', ChoiceType::class, [
                'label' => $this->trans('Mail method', 'Admin.Advparameters.Feature'),
                'attr' => [
                    'class' => 'js-email-method',
                    'data-smtp-mail-method' => MailOption::METHOD_SMTP,
                ],
                'expanded' => true,
                'multiple' => false,
                'choices' => $this->mailMethodChoiceProvider->getChoices(),
                'multistore_configuration_key' => 'PS_MAIL_METHOD',
            ])
            ->add('subject_prefix', SwitchType::class, [
                'label' => $this->trans('Enable the store name as a prefix in the email\'s subject', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_MAIL_SUBJECT_PREFIX',
            ])
            ->add('mail_type', ChoiceType::class, [
                'label' => $this->trans('Select the type of email content', 'Admin.Advparameters.Feature'),
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    $this->trans('Send email in HTML format', 'Admin.Advparameters.Feature') => MailOption::TYPE_HTML,
                    $this->trans('Send email in text format', 'Admin.Advparameters.Feature') => MailOption::TYPE_TXT,
                    $this->trans('Both', 'Admin.Advparameters.Feature') => MailOption::TYPE_BOTH,
                    $this->trans('Both, with text format automatically generated', 'Admin.Advparameters.Feature') => MailOption::TYPE_BOTH_AUTOMATIC_TEXT,
                ],
                'multistore_configuration_key' => 'PS_MAIL_TYPE',
            ])
            ->add('log_emails', SwitchType::class, [
                'label' => $this->trans('Log Emails', 'Admin.Advparameters.Feature'),
                'multistore_configuration_key' => 'PS_LOG_EMAILS',
            ])
            ->add('dkim_enable', SwitchType::class, [
                'attr' => ['class' => 'js-dkim-enable'],
                'choices' => [
                    'Disabled' => false,
                    'Enabled' => true,
                ],
                'label' => $this->trans('DKIM signing', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Enable DKIM, fill in the fields and give it a try. If no email is sent, check logs.', 'Admin.Advparameters.Help'),
                'multistore_configuration_key' => 'PS_MAIL_DKIM_ENABLE',
            ])
            ->add('smtp_config', SmtpConfigurationType::class)
            ->add('dkim_config', DkimConfigurationType::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return MultistoreConfigurationType::class;
    }
}
