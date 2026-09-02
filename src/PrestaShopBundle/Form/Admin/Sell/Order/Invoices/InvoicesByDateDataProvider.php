<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use DateTime;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Order\OrderInvoiceDataProviderInterface;

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
        return $this->validate($data);
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

        $dateFrom = date_create($data['date_from']);
        $dateTo = date_create($data['date_to']);

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
