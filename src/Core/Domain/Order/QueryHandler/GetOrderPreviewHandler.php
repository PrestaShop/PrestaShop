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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderPreview;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\InvoiceDetails;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPreview;

final class GetOrderPreviewHandler implements GetOrderPreviewHandlerInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var int
     */
    private $contextLangId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int $contextLangId
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        int $contextLangId
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->contextLangId = $contextLangId;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GetOrderPreview $query): OrderPreview
    {
        $orderId = $query->getOrderId()->getValue();
        $qb = $this->getCommonQueryBuilder($orderId);

        return new OrderPreview(
            $this->getInvoiceDetails($qb)
        );
    }

    private function getInvoiceDetails(QueryBuilder $qb)
    {
        $qb->leftJoin(
            'o',
            $this->dbPrefix . 'address',
            'addr',
            'o.id_address_invoice = addr.id_address'
        );

        $qb->leftJoin(
            'addr',
            $this->dbPrefix . 'country_lang',
            'countryLang',
            'addr.id_country = countryLang.id_country AND countryLang.id_lang = :contextLangId'
        );

        $qb->select('
            addr.firstname,
            addr.lastname,
            addr.company,
            addr.address1,
            addr.address2,
            addr.city,
            countryLang.name AS country,
            addr.phone,
            c.email
        ');

        $invoiceResult = $qb->execute()->fetch();

        return new InvoiceDetails(
            $invoiceResult['firstname'],
            $invoiceResult['lastname'],
            $invoiceResult['address1'],
            $invoiceResult['city'],
            $invoiceResult['country'],
            $invoiceResult['email'],
            $invoiceResult['phone'],
            $invoiceResult['address2'],
            $invoiceResult['company']
        );
    }

    private function getCommonQueryBuilder(int $orderId)
    {
        $qb = $this->connection->createQueryBuilder()
            ->from($this->dbPrefix . 'order_invoice', 'oi')
            ->where('oi.id_order = :orderId')
            ->setParameter('orderId', $orderId)
            ->setParameter('contextLangId', $this->contextLangId)
            ->setMaxResults(1);

        $qb->leftJoin(
            'oi',
            $this->dbPrefix . 'orders',
            'o',
            'oi.id_order = o.id_order'
        );

        $qb->leftJoin(
            'o',
            $this->dbPrefix . 'customer',
            'c',
            'o.id_customer = c.id_customer'
        );

        return $qb;
    }
}
