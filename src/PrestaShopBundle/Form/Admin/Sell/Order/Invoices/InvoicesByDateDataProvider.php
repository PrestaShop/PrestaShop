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

use DateTime;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Order\OrderInvoiceDataProviderInterface;
use PrestaShopBundle\Form\Exception\DataProviderException;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataError;
use PrestaShopBundle\Form\Exception\InvalidConfigurationDataErrorCollection;

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
        $date = (new DateTime())->format('Y-m-d');

        return [
            'date_from' => $date,
            'date_to' => $date,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        // This form doesn't need to save any data, so it only validates the data
        $this->validate($data);

        return [];
    }

    /**
     * Perform validations on form data.
     *
     * @param array $data
     *
     * @return void
     */
    private function validate(array $data)
    {
        $errorCollection = new InvalidConfigurationDataErrorCollection();

        if (!isset($data[GenerateByDateType::FIELD_DATE_FROM]) || false === $data[GenerateByDateType::FIELD_DATE_FROM]) {
            $errorCollection->add(new InvalidConfigurationDataError(InvalidConfigurationDataError::ERROR_INVALID_DATE_FROM, GenerateByDateType::FIELD_DATE_FROM));
        }

        if (!isset($data[GenerateByDateType::FIELD_DATE_TO]) || false === $data[GenerateByDateType::FIELD_DATE_TO]) {
            $errorCollection->add(new InvalidConfigurationDataError(InvalidConfigurationDataError::ERROR_INVALID_DATE_TO, GenerateByDateType::FIELD_DATE_TO));
        }

        if (!$errorCollection->isEmpty()) {
            throw new DataProviderException('Invalid invoices by date form', 0, null, $errorCollection);
        }

        $dateFrom = date_create($data[GenerateByDateType::FIELD_DATE_FROM]);
        $dateTo = date_create($data[GenerateByDateType::FIELD_DATE_TO]);

        if (!$this->orderInvoiceDataProvider->getByDateInterval($dateFrom, $dateTo)) {
            $errorCollection->add(
                new InvalidConfigurationDataError(
                    InvalidConfigurationDataError::ERROR_NO_INVOICES_FOUND,
                    GenerateByDateType::FIELD_DATE_TO
                )
            );
        }

        if (!$errorCollection->isEmpty()) {
            throw new DataProviderException('Invalid invoices by date form', 0, null, $errorCollection);
        }
    }
}
