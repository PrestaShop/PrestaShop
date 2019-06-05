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

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class is responsible of managing the data manipulated using invoice options form
 * in "Sell > Orders > Invoices" page.
 */
final class InvoiceOptionsDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $invoiceOptionsConfiguration;

    /**
     * @var int
     */
    private $nextInvoiceNumber;

    /**
     * @param DataConfigurationInterface $invoiceOptionsConfiguration
     * @param int $nextInvoiceNumber next available invoice number
     */
    public function __construct(
        DataConfigurationInterface $invoiceOptionsConfiguration,
        $nextInvoiceNumber
    ) {
        $this->invoiceOptionsConfiguration = $invoiceOptionsConfiguration;
        $this->nextInvoiceNumber = $nextInvoiceNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return [
            'invoice_options' => $this->invoiceOptionsConfiguration->getConfiguration(),
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

        return $this->invoiceOptionsConfiguration->updateConfiguration($data['invoice_options']);
    }

    /**
     * Perform validations on form data.
     *
     * @param array $data
     *
     * @return array Array of errors if any
     */
    private function validate(array $data)
    {
        $errors = [];
        $invoiceNumber = $data['invoice_options']['invoice_number'];

        if ($invoiceNumber > 0 && $invoiceNumber <= $this->nextInvoiceNumber) {
            $errors[] = [
                'key' => 'Invalid invoice number.',
                'domain' => 'Admin.Orderscustomers.Notification',
                'parameters' => [],
            ];
        }

        return $errors;
    }
}
