<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\CustomerService\CustomerThread;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

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
