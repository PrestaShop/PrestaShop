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

use Currency;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Responsible for creating form for price reduction
 */
class ReductionType extends CommonAbstractType
{
    /**
     * @var array
     */
    private $priceReductionTypeChoices;

    /**
     * @var Currency
     */
    private $defaultCurrency;

    /**
     * @var EventSubscriberInterface
     */
    private $eventSubscriber;

    public function __construct(
        array $priceReductionTypeChoices,
        Currency $defaultCurrency,
        EventSubscriberInterface $eventSubscriber
    ) {
        $this->priceReductionTypeChoices = $priceReductionTypeChoices;
        $this->defaultCurrency = $defaultCurrency;
        $this->eventSubscriber = $eventSubscriber;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'placeholder' => false,
                'required' => false,
                'choices' => $options['choices'],
            ])
            ->add('value', MoneyType::class, [
                'scale' => $options['scale'],
                'currency' => $this->defaultCurrency->iso_code,
                'attr' => [
                    'data-currency' => $this->defaultCurrency->symbol,
                ],
            ]);

        $builder->addEventSubscriber($this->eventSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => $this->priceReductionTypeChoices,
            'scale' => 6,
        ]);
    }
}
