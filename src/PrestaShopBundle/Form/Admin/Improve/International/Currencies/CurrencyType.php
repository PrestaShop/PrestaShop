<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Currencies;

use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class CurrencyType
 */
class CurrencyType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var array
     */
    private $allCurrencies;

    /**
     * @var bool
     */
    private $isShopFeatureEnabled;

    /**
     * @param array $allCurrencies
     * @param bool $isShopFeatureEnabled
     */
    public function __construct(array $allCurrencies, $isShopFeatureEnabled)
    {
        $this->allCurrencies = $allCurrencies;
        $this->isShopFeatureEnabled = $isShopFeatureEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('iso_code', ChoiceType::class, [
                'choices' => $this->allCurrencies,
                'choice_translation_domain' => false,
            ])
            ->add('exchange_rate', NumberType::class, [
                'scale' => 6,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->trans('Exchange rate', [], 'Admin.International.Feature')),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new GreaterThan([
                        'value' => 0,
                        'message' => $this->trans(
                            'This value should be greater than %value%',
                            [
                                '%value%' => 0,
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'invalid_message' => $this->trans(
                    'This field is invalid, it must contain numeric values',
                    [],
                    'Admin.Notifications.Error'
                ),
            ])
            ->add('active', SwitchType::class, [
                'required' => false,
            ])
        ;

        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->trans('Shop association', [], 'Admin.Global')),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);
        }
    }
}
