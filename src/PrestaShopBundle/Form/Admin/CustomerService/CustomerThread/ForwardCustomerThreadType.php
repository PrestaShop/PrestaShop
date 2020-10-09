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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\CustomerService\CustomerThread;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Email;

/**
 * Builds form for forwarding customer thread
 */
class ForwardCustomerThreadType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FormChoiceProviderInterface
     */
    private $employeeChoiceProvider;

    /**
     * @param TranslatorInterface $translator
     * @param FormChoiceProviderInterface $employeeChoiceProvider
     */
    public function __construct(TranslatorInterface $translator, FormChoiceProviderInterface $employeeChoiceProvider)
    {
        $this->translator = $translator;
        $this->employeeChoiceProvider = $employeeChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $employeeChoices = $this->employeeChoiceProvider->getChoices();
        $employeeChoices[$this->translator->trans('Someone else', [], 'Admin.Orderscustomers.Feature')] = 0;

        $builder
            ->add('employee_id', ChoiceType::class, [
                'choices' => $employeeChoices,
                'translation_domain' => false,
            ])
            ->add('someone_else_email', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new Email([
                        'message' => $this->translator->trans(
                            'The email address is invalid.',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('comment', TextareaType::class, [
                'required' => false,
            ])
        ;
    }
}
