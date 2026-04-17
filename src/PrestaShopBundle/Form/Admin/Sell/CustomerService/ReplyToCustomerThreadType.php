<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ReplyToCustomerThreadType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reply_message', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'Reply message is required',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new CleanHtml([
                        'message' => $this->translator->trans(
                            'Reply message is invalid',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
        ;
    }
}
