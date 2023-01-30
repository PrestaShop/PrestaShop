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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use OrderReturn;
use PDF;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use RuntimeException;
use Symfony\Component\Translation\TranslatorInterface;
use Validate;

/**
 * Generates invoice for given order.
 */
final class OrderReturnPdfGenerator implements PDFGeneratorInterface
{
    private const ORDER_RETURN_STATE_WAITING_FOR_PACKAGE = 2;
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
    public function generatePDF(array $orderReturnId)
    {
        if (count($orderReturnId) !== 1) {
            throw new CoreException(sprintf('"%s" supports generating order return for single order only.', get_class($this)));
        }

        $orderReturnId = reset($orderReturnId);
        $orderReturn = new OrderReturn((int) $orderReturnId);
        if (!Validate::isLoadedObject($orderReturn)) {
            throw new RuntimeException($this->translator->trans('The order return cannot be found within your database.', [], 'Admin.Orderscustomers.Notification'));
        }

        if ($orderReturn->state < self::ORDER_RETURN_STATE_WAITING_FOR_PACKAGE) {
            throw new RuntimeException($this->translator->trans('The order return was not confirmed.', [], 'Admin.Orderscustomers.Notification'));
        }

        $pdf = new PDF($orderReturn, PDF::TEMPLATE_ORDER_RETURN, Context::getContext()->smarty);

        return $pdf->render(false);
    }
}
