<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Order\Invoices;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class is responsible of managing the data manipulated using invoice generation by order status form
 * in "Sell > Orders > Invoices" page.
 */
final class InvoicesByStatusDataProvider implements FormDataProviderInterface
{
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
     */
    private function validate(array $data)
    {
        $errors = [];
        $orderStates = $data['order_states'];

        if (!is_array($orderStates) || !count($orderStates)) {
            $errors[] = [
                'key' => 'You must select at least one order status.',
                'domain' => 'Admin.Orderscustomers.Notification',
                'parameters' => [],
            ];
        }

        return $errors;
    }
}
