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

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use Doctrine\DBAL\Connection;
use PDO;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShop\PrestaShop\Core\Util\File\FileSizeConverter;
use PrestaShopBundle\Translation\TranslatorAwareTrait;

/**
 * Decorates attachment grid data factory
 */
final class AttachmentGridDataFactoryDecorator implements GridDataFactoryInterface
{
    use TranslatorAwareTrait;

    /**
     * @var GridDataFactoryInterface
     */
    private $attachmentDoctrineGridDataFactory;

    /**
     * @var int
     */
    private $employeeIdLang;

    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    /**
     * @var FileSizeConverter
     */
    private $fileSizeConverter;

    /**
     * @param GridDataFactoryInterface $attachmentDoctrineGridDataFactory
     * @param int $employeeIdLang
     * @param Connection $connection
     * @param string $dbPrefix
     * @param FileSizeConverter $fileSizeConverter
     */
    public function __construct(
        GridDataFactoryInterface $attachmentDoctrineGridDataFactory,
        int $employeeIdLang,
        Connection $connection,
        string $dbPrefix,
        FileSizeConverter $fileSizeConverter
    ) {
        $this->attachmentDoctrineGridDataFactory = $attachmentDoctrineGridDataFactory;
        $this->employeeIdLang = $employeeIdLang;
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->fileSizeConverter = $fileSizeConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(SearchCriteriaInterface $searchCriteria)
    {
        $attachmentData = $this->attachmentDoctrineGridDataFactory->getData($searchCriteria);

        $attachmentRecords = $this->applyModifications($attachmentData->getRecords());

        return new GridData(
            $attachmentRecords,
            $attachmentData->getRecordsTotal(),
            $attachmentData->getQuery()
        );
    }

    /**
     * @param RecordCollectionInterface $attachments
     *
     * @return RecordCollection
     */
    private function applyModifications(RecordCollectionInterface $attachments): RecordCollection
    {
        $modifiedAttachments = [];

        foreach ($attachments as $attachment) {
            if ((int) $attachment['products'] > 0) {
                $productNamesArray = $this->getProductNames($attachment['id_attachment']);
                $productNames = implode(', ', $productNamesArray);
                $attachment['dynamic_message'] = $this->trans(
                    'This file is associated with the following products. Are you sure you want to delete it?',
                    [],
                    'Admin.Catalog.Notification'
                );
                $attachment['dynamic_message'] .= PHP_EOL . PHP_EOL . $productNames;
            }

            $attachment['file_size'] = $this->fileSizeConverter->convert((int) $attachment['file_size']);
            $attachment['products'] .= ' ' . $this->trans('product(s)', [], 'Admin.Catalog.Feature');

            $modifiedAttachments[] = $attachment;
        }

        return new RecordCollection($modifiedAttachments);
    }

    /**
     * @param string $attachmentId
     *
     * @return array
     */
    private function getProductNames(string $attachmentId): array
    {
        $qb = $this->connection->createQueryBuilder();

        $qb->select('DISTINCT pl.`name`')
            ->from($this->dbPrefix . 'product_attachment', 'pa')
            ->leftJoin(
                'pa',
                $this->dbPrefix . 'product_lang',
                'pl',
                'pa.`id_product` = pl.`id_product` AND pl.`id_lang` = :langId'
            )
            ->where('pa.`id_attachment` = :attachmentId')
            ->setParameter('attachmentId', $attachmentId)
            ->setParameter('langId', $this->employeeIdLang);

        return $qb->execute()->fetchAll(PDO::FETCH_COLUMN);
    }
}
