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

namespace PrestaShopBundle\Form\Admin\Sell\Order\CreditSlip;

use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

/**
 * Defines form for generating Credit slip PDF
 */
final class GeneratePdfByDateType extends CommonAbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $dateFormat = 'Y-m-d';
        $nowDate = (new \DateTime())->format($dateFormat);

        $blankMessage = $this->translator->trans('This field is required', [], 'Admin.Notifications.Error');
        $invalidDateMessage = $this->translator->trans('Invalid date format.', [], 'Admin.Notifications.Error');

        $builder
            ->add('from', DatePickerType::class, [
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
