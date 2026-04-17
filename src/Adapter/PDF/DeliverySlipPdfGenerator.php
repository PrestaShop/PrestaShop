<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use Order;
use PDF;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use RuntimeException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Validate;

/**
 * Generates delivery slip for given order
 *
 * @internal
 */
final class DeliverySlipPdfGenerator implements PDFGeneratorInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePDF(array $orderId): string
    {
        if (count($orderId) !== 1) {
            throw new CoreException(sprintf('"%s" supports generating delivery slip for single order only.', self::class));
        }

        $orderId = reset($orderId);
        $order = new Order((int) $orderId);

        if (!Validate::isLoadedObject($order)) {
            throw new RuntimeException($this->translator->trans('The order cannot be found within your database.', [], 'Admin.Orderscustomers.Notification'));
        }

        $order_invoice_collection = $order->getInvoicesCollection();

        $pdf = new PDF($order_invoice_collection, PDF::TEMPLATE_DELIVERY_SLIP, Context::getContext()->smarty);

        return $pdf->render(true);
    }
}
