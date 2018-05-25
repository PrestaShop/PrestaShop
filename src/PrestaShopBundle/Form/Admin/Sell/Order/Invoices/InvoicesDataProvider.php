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

use PrestaShop\PrestaShop\Adapter\Invoice\OrderInvoiceDataProvider;
use PrestaShop\PrestaShop\Adapter\Validate;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class is responsible of managing the data manipulated using forms
 * in "Sell > Orders > Invoices" page.
 */
final class InvoicesDataProvider implements FormDataProviderInterface
{
    /**
     * @var Validate
     */
    private $validate;

    /**
     * @var OrderInvoiceDataProvider
     */
    private $orderInvoiceDataProvider;

    public function __construct(
        Validate $validate,
        OrderInvoiceDataProvider $orderInvoiceDataProvider
    ) {
        $this->validate = $validate;
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
        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return [];
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
        $dateFrom = $data['generate_by_date']['date_from'];
        $dateTo = $data['generate_by_date']['date_to'];

        if (!$this->validate->isValidDate($dateFrom)) {
            $errors[] = [
                'key' => 'Invalid "From" date',
                'domain' => 'Admin.Orderscustomers.Notification',
                'parameters' => [],
            ];
        }

        if (!$this->validate->isValidDate($dateTo)) {
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
