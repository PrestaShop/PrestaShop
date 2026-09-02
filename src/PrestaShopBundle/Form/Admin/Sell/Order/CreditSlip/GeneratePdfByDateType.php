<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\CreditSlip;

use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Backwards compatibility break introduced in 1.7.8.0 due to extension of TranslationAwareType instead of using translator as dependency.
 *
 * Defines form for generating Credit slip PDF
 */
final class GeneratePdfByDateType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateFormat = 'Y-m-d';
        $nowDate = (new \DateTime())->format($dateFormat);

        $blankMessage = $this->trans('This field is required', 'Admin.Notifications.Error');
        $invalidDateMessage = $this->trans('Invalid date format.', 'Admin.Notifications.Error');
        $dateHintTrans = $this->trans('Format: 2011-12-31 (inclusive).', 'Admin.Global');

        $builder
            ->add('from', DatePickerType::class, [
                'label' => $this->trans('From', 'Admin.Global'),
                'help' => $dateHintTrans,
                'data' => $nowDate,
                'constraints' => [
                    new NotBlank([
                        'message' => $blankMessage,
                    ]),
                    new DateTime([
                        'format' => $dateFormat,
                        'message' => $invalidDateMessage,
                    ]),
                ],
            ])
            ->add('to', DatePickerType::class, [
                'label' => $this->trans('To', 'Admin.Global'),
                'help' => $dateHintTrans,
                'data' => $nowDate,
                'constraints' => [
                    new NotBlank([
                        'message' => $blankMessage,
                    ]),
                    new DateTime([
                        'format' => $dateFormat,
                        'message' => $invalidDateMessage,
                    ]),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'constraints' => [
                new Valid(),
            ],
        ]);
    }
}
