<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\DataProvider\OrderInvoiceDataProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class is responsible of managing the data manipulated using invoice generation by date form
 * in "Sell > Orders > Invoices" page.
 */
final class InvoicesByDateDataProvider implements FormDataProviderInterface
{
    /**
     * @var OrderInvoiceDataProviderInterface
     */
    private $orderInvoiceDataProvider;

    public function __construct(OrderInvoiceDataProviderInterface $orderInvoiceDataProvider)
    {
        $this->orderInvoiceDataProvider = $orderInvoiceDataProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'generate_by_date' => [
                'date_from' => date('Y-m-d'),
                'date_to' => date('Y-m-d'),
            ],
        ];
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
     * Perform validations on form data
     *
     * @param array $data
     *
     * @return array Array of errors if any
     */
    private function validate(array $data)
    {
        $errors = [];

        $dateFrom = date_create($data['generate_by_date']['date_from']);
        $dateTo = date_create($data['generate_by_date']['date_to']);

        if (false === $dateFrom) {
            $errors[] = [
                'key' => 'Invalid "From" date',
                'domain' => 'Admin.Orderscustomers.Notification',
                'parameters' => [],
            ];
        }

        if (false === $dateTo) {
            $errors[] = [
                'key' => 'Invalid "To" date',
                'domain' => 'Admin.Orderscustomers.Notification',
                'parameters' => [],
            ];
        }

        if (empty($errors) && !$this->orderInvoiceDataProvider->getByDateInterval($dateFrom, $dateTo)) {
            $errors[] = [
                'key' => 'No invoice has been found for this period.',
                'domain' => 'Admin.Orderscustomers.Notification',
                'parameters' => [],
            ];
        }

        return $errors;
    }
}
