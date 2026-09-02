<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        return $this->invoiceOptionsConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        if ($errors = $this->validate($data)) {
            return $errors;
        }

        return $this->invoiceOptionsConfiguration->updateConfiguration($data);
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
        $invoiceNumber = $data['invoice_number'];

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
