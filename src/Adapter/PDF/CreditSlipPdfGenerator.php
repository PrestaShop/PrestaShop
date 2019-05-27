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

namespace PrestaShop\PrestaShop\Adapter\PDF;

use Context;
use Db;
use ObjectModel;
use PDF;
use PrestaShop\PrestaShop\Adapter\Entity\OrderSlip;
use PrestaShop\PrestaShop\Core\PDF\PDFGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Validate;

final class CreditSlipPdfGenerator implements PDFGeneratorInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;
    private $dbPrefix;

    public function __construct(TranslatorInterface $translator, $dbPrefix)
    {
        $this->translator = $translator;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * Generates PDF out of given object and template using legacy generator.
     *
     * @param array $objectCollection collection of objects
     */
    public function generatePDF(array $creditSlipIds)
    {
        if (count($creditSlipIds) > 1) {
            $slipIds = '(' . implode(',', $creditSlipIds) . ')';
            $slipsList = Db::getInstance()->executeS(
                'SELECT * FROM ' . $this->dbPrefix . 'order_slip WHERE id_order_slip IN ' . $slipIds
            );
            $slipsList = ObjectModel::hydrateCollection('OrderSlip', $slipsList);
        } else {
            $slipsList = new OrderSlip((int) $creditSlipIds);
            if (!Validate::isLoadedObject($slipsList)) {
                die($this->translator->trans(
                    'The credit slip cannot be found within your database.',
                    [],
                    'Admin.Orderscustomers.Notification'
                ));
            }
        }
        $pdf = new PDF($slipsList, PDF::TEMPLATE_ORDER_SLIP, Context::getContext()->smarty);
        $pdf->render();
    }
}
