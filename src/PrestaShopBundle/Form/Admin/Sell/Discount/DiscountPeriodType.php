<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use DateTime;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DateRange;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShopBundle\Form\Admin\Type\CardType;
use PrestaShopBundle\Form\Admin\Type\DateRangeType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscountPeriodType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('valid_date_range', DateRangeType::class, [
                'label' => false,
                'required' => false,
                'date_format' => DateRangeType::DEFAULT_DATE_TIME_FORMAT,
                'placeholder' => DateRangeType::DEFAULT_DATE_TIME_FORMAT,
                'default_end_value' => (new DateTime())->modify('+1 month')->setTime(23, 59, 59)->format(DateTimeUtil::DEFAULT_DATETIME_FORMAT),
                'constraints' => [
                    new DateRange([
                        'message' => $this->trans(
                            'The expiration date must be after start date',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('period_never_expires', CheckboxType::class, [
                'label' => $this->trans('Period never expires', 'Admin.Catalog.Feature'),
                'required' => false,
                'label_attr' => [
                    'class' => 'form-check-label',
                ],
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event): void {
            $data = $event->getData();
            if (!empty($data['period_never_expires'])) {
                $data['valid_date_range']['to'] = null;
                $event->setData($data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Select period', 'Admin.Catalog.Feature'),
            'required' => false,
        ]);
    }

    public function getParent()
    {
        return CardType::class;
    }
}
