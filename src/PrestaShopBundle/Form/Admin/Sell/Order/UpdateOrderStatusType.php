<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order;

use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class UpdateOrderStatusType extends AbstractType
{
    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $statusChoiceProvider;

    /**
     * @var array
     */
    private $statusChoiceAttributes;

    /**
     * @param ConfigurableFormChoiceProviderInterface $statusChoices
     * @param array $statusChoiceAttributes
     */
    public function __construct(
        ConfigurableFormChoiceProviderInterface $statusChoiceProvider,
        array $statusChoiceAttributes
    ) {
        $this->statusChoiceProvider = $statusChoiceProvider;
        $this->statusChoiceAttributes = $statusChoiceAttributes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choiceProviderParams = [];
        if (!empty($options['data']['new_order_status_id'])) {
            $choiceProviderParams = ['current_state' => $options['data']['new_order_status_id']];
        }
        $builder
            ->add('new_order_status_id', ChoiceType::class, [
                'required' => false,
                'placeholder' => false,
                'choices' => $this->statusChoiceProvider->getChoices($choiceProviderParams),
                'choice_attr' => $this->statusChoiceAttributes,
                'translation_domain' => false,
            ])
        ;
    }
}
