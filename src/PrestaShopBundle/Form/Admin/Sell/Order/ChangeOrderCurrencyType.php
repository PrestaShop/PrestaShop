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

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeOrderCurrencyType extends AbstractType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $currencyChoiceProvider;

    /**
     * @param FormChoiceProviderInterface $currencyChoiceProvider
     */
    public function __construct(FormChoiceProviderInterface $currencyChoiceProvider)
    {
        $this->currencyChoiceProvider = $currencyChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('new_currency_id', ChoiceType::class, [
                'choices' => $this->getCurrencyChoices($options['current_currency_id']),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'current_currency_id' => null,
            ])
            ->setAllowedTypes('current_currency_id', ['int', 'null'])
        ;
    }

    /**
     * @param int|null $currentCurrencyId
     *
     * @return array
     */
    private function getCurrencyChoices(?int $currentCurrencyId): array
    {
        $choices = $this->currencyChoiceProvider->getChoices();

        if (null === $currentCurrencyId) {
            return $choices;
        }

        $currentCurrencyKey = array_search($currentCurrencyId, $choices, true);

        if ($currentCurrencyKey) {
            unset($choices[$currentCurrencyKey]);
        }

        return $choices;
    }
}
