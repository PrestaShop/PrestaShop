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
use PrestaShopBundle\Form\FormCloner;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Contracts\Translation\TranslatorInterface;

class DateRangeLocalType extends DateRangeType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var FormCloner
     */
    protected $formCloner;

    /**
     * @var string
     */
    protected $dateFormatFull;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(
        TranslatorInterface $translator,
        FormCloner $formCloner,
        string $dateFormatFull
    ) {
        $this->translator = $translator;
        $this->formCloner = $formCloner;
        $this->dateFormatFull = $dateFormatFull;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $now = new DateTime();
        $builder
            ->add('from', DatePickerType::class, [
                'required' => false,
                'label' => $this->translator->trans('Start date', [], 'Admin.Global'),
                'attr' => [
                    'placeholder' => $options['attr']['placeholder'] ? $options['attr']['placeholder'] : $this->translator->trans('YY-MM-DD', [], 'Admin.Global'),
                    'class' => 'from date-range-start-date',
                    'data-default-value' => $now->format($this->dateFormatFull),
                ],
                'date_format' => $options['date_format'],
            ])
            ->add('to', DatePickerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => $options['attr']['placeholder'] ? $options['attr']['placeholder'] : $this->translator->trans('YY-MM-DD', [], 'Admin.Global'),
                    'class' => 'to date-range-end-date',
                    'data-default-value' => $now->format($this->dateFormatFull),
                ],
                'label' => $this->translator->trans('End date', [], 'Admin.Global'),
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

        $builder->get('from')->addModelTransformer(new CallbackTransformer(
            function ($from) {
                if ($from !== null && $from != '0000-00-00 00:00:00') {
                    $dateTime = DateTime::createFromFormat('Y-m-d h:i:s', $from);

                    return $dateTime->format($this->dateFormatFull);
                }

                return $from;
            },
            function ($from) {
                if ($from !== null) {
                    $dateTime = DateTime::createFromFormat($this->dateFormatFull, $from);

                    return $dateTime->format('Y-m-d h:i:s');
                }

                return $from;
            }
        ));

        $builder->get('to')->addModelTransformer(new CallbackTransformer(
            function ($to) {
                if ($to !== null) {
                    $dateTime = DateTime::createFromFormat('Y-m-d h:i:s', $to);

                    return $dateTime->format($this->dateFormatFull);
                }

                return $to;
            },
            function ($to) {
                if ($to !== null) {
                    $dateTime = DateTime::createFromFormat($this->dateFormatFull, $to);

                    return $dateTime->format('Y-m-d h:i:s');
                }

                return $to;
            }
        ));
    }
}
