<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
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
     * @param GridDataFactoryInterface $attachmentDoctrineGridDataFactory
     * @param LanguageContext $languageContext
     * @param Connection $connection
     * @param string $dbPrefix
     * @param FileSizeConverter $fileSizeConverter
     */
    public function __construct(
        private GridDataFactoryInterface $attachmentDoctrineGridDataFactory,
        private LanguageContext $languageContext,
        private Connection $connection,
        private string $dbPrefix,
        private FileSizeConverter $fileSizeConverter
    ) {
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
            ->setParameter('langId', $this->languageContext->getId());

        return $qb->executeQuery()->fetchFirstColumn();
    }
}
