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

namespace PrestaShopBundle\Form\Admin\Type;

use DateTime;
use PrestaShop\PrestaShop\Core\Util\DateTime\DateTime as DateTimeUtil;
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSetDataEvent;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class DateRangeType
 * 
 * @link https://devdocs.prestashop-project.org/8/development/components/form/types-reference/date-range/
 */
class DateRangeType extends AbstractType
{
    /**
     * These date format constants are used for front-end part and have different format compared to php DateTime class.
     */
    public const DEFAULT_DATE_FORMAT = 'YYYY-MM-DD';
    public const DEFAULT_DATE_TIME_FORMAT = 'YYYY-MM-DD HH:mm:ss';

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var FormCloner
     */
    protected $formCloner;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator,
        FormCloner $formCloner
    ) {
        $this->translator = $translator;
        $this->formCloner = $formCloner;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('from', DatePickerType::class, [
                'required' => false,
                'label' => $options['label_from'],
                'attr' => [
                    'placeholder' => $options['placeholder'],
                    'class' => 'from date-range-start-date',
                ],
                'date_format' => $options['date_format'],
            ])
            ->add('to', DatePickerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => $options['placeholder'],
                    'class' => 'to date-range-end-date',
                    'data-default-value' => $options['default_end_value'],
                ],
                'label' => $options['label_to'],
                'date_format' => $options['date_format'],
            ])
        ;

        if ($options['has_unlimited_checkbox']) {
            $builder->add('unlimited', CheckboxType::class, [
                'label' => $this->translator->trans('Unlimited', [], 'Admin.Global'),
                'required' => false,
                'attr' => [
                    'class' => 'date-range-unlimited',
                ],
            ]);

            $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'adaptUnlimited']);
            $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'adaptUnlimited']);
        }
    }

    public function adaptUnlimited(FormEvent $event): void
    {
        $data = $event->getData();
        $form = $event->getForm();
        if (DateTimeUtil::isNull($data['to'] ?? null)) {
            $data['unlimited'] = true;
            $data['to'] = null;
            $event->setData($data);

            // Force disable state on end date field only on first rendering not submit
            if ($event instanceof PreSetDataEvent) {
                $form->add($this->formCloner->cloneForm($form->get('to'), [
                    'disabled' => true,
                ]));
            }
        } elseif ($event instanceof PreSubmitEvent) {
            // Re-enable state on end date field on submit in case it was previously disabled on pre-set data event
            $form->add($this->formCloner->cloneForm($form->get('to'), [
                'disabled' => false,
            ]));
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'date_format' => self::DEFAULT_DATE_FORMAT,
            'placeholder' => self::DEFAULT_DATE_FORMAT,
            'has_unlimited_checkbox' => false,
            'default_end_value' => (new DateTime())->format('Y-m-d'),
            'label_from' => $this->translator->trans('Start date', [], 'Admin.Global'),
            'label_to' => $this->translator->trans('End date', [], 'Admin.Global'),
        ]);
        $resolver
            ->setAllowedTypes('date_format', 'string')
            ->setAllowedTypes('placeholder', 'string')
            ->setAllowedTypes('label_from', 'string')
            ->setAllowedTypes('label_to', 'string')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'date_range';
    }
}
