<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Email;

use PrestaShop\PrestaShop\Core\Email\MailOption;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
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
            ])
            ->add('mail_method', ChoiceType::class, [
                'attr' => [
                    'class' => 'js-email-method',
                    'data-smtp-mail-method' => MailOption::METHOD_SMTP,
                ],
                'expanded' => true,
                'multiple' => false,
                'choices' => $this->mailMethodChoiceProvider->getChoices(),
            ])
            ->add('subject_prefix', SwitchType::class, [
                'label' => $this->trans('Enable the store name as a prefix in the email\'s subject', 'Admin.Advparameters.Feature'),
            ])
            ->add('mail_type', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    $this->trans('Send email in HTML format', 'Admin.Advparameters.Feature') => MailOption::TYPE_HTML,
                    $this->trans('Send email in text format', 'Admin.Advparameters.Feature') => MailOption::TYPE_TXT,
                    $this->trans('Both', 'Admin.Advparameters.Feature') => MailOption::TYPE_BOTH,
                ],
            ])
            ->add('log_emails', SwitchType::class, [
                'label' => $this->trans('Log Emails', 'Admin.Advparameters.Feature'),
            ])
            ->add('dkim_enable', SwitchType::class, [
                'attr' => ['class' => 'js-dkim-enable'],
                'choices' => [
                    'Disabled' => false,
                    'Enabled' => true,
                ],
                'label' => $this->trans('DKIM signing', 'Admin.Advparameters.Feature'),
                'help' => $this->trans('Enable DKIM, fill in the fields and give it a try. If no email is sent, check logs.', 'Admin.Advparameters.Help'),
            ])
            ->add('smtp_config', SmtpConfigurationType::class)
            ->add('dkim_config', DkimConfigurationType::class);
    }
}
