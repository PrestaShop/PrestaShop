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

namespace PrestaShopBundle\Form\Admin\Sell\Address;

use PrestaShopBundle\Form\Admin\Type\Material\MaterialChoiceTableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Defines form for address required fields modification (Sell > Customers > Addresses)
 */
class RequiredFieldsAddressType extends AbstractType
{
    /**
     * @var array
     */
    private $addressRequiredFieldsChoices;

    /**
     * @param array $addressRequiredFieldsChoices
     */
    public function __construct(array $addressRequiredFieldsChoices)
    {
        $this->addressRequiredFieldsChoices = $addressRequiredFieldsChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('required_fields', MaterialChoiceTableType::class, [
                'label' => false,
                'choices' => $this->addressRequiredFieldsChoices,
            ])
        ;
    }
}
