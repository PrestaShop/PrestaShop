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

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Exception\TypeException;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShopBundle\Form\Exception\DataProviderException;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;

/**
 * Class is responsible of managing the data manipulated using invoice generation by order status form
 * in "Sell > Orders > Invoices" page.
 */
final class InvoicesByStatusDataProvider implements FormDataProviderInterface
{
    public const ERROR_NO_ORDER_STATE_SELECTED = 'error_no_order_state_selected';

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        // This form doesn't need to save any data, so it only validates the data
        return $this->validate($data);
    }

    /**
     * Perform validations on form data.
     *
     * @param array $data
     *
     * @return array Array of errors if any
     *
     * @throws DataProviderException|TypeException
     */
    private function validate(array $data)
    {
        $errors = [];

        if (!isset($data[GenerateByStatusType::FIELD_ORDER_STATES])
            || !is_array($data[GenerateByStatusType::FIELD_ORDER_STATES])
            || !count($data[GenerateByStatusType::FIELD_ORDER_STATES])) {
            $errorCollection = new InvalidConfigurationDataErrorCollection();

            $errorCollection->add(
                new InvalidConfigurationDataError(
                    static::ERROR_NO_ORDER_STATE_SELECTED,
                    GenerateByStatusType::FIELD_ORDER_STATES
                )
            );

            throw new DataProviderException('No order state selected', 0, null, $errorCollection);
        }

        return $errors;
    }
}
