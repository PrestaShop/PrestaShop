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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\CustomerService\MerchandiseReturn;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Form type for merchandise returns options
 */
class MerchandiseReturnType extends AbstractType
{
    /**
     * @var array
     */
    private $stateChoices;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * MerchandiseReturnType constructor.
     *
     * @param array $stateChoices
     * @param TranslatorInterface $translator
     */
    public function __construct(
        array $stateChoices,
        TranslatorInterface $translator
    )
    {
        $this->stateChoices = $stateChoices;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('merchandise_return_order_state', ChoiceType::class, [
                'required' => true,
                'choices' => $this->stateChoices,
                'label' => $this->translator->trans('Order status', [], 'Admin.Shopparameters.Feature')
            ])
        ;
    }
}
