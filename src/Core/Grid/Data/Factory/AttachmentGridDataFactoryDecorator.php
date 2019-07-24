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

namespace PrestaShop\PrestaShop\Core\Grid\Data\Factory;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Grid\Data\GridData;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollection;
use PrestaShop\PrestaShop\Core\Grid\Record\RecordCollectionInterface;
use PrestaShop\PrestaShop\Core\Grid\Search\SearchCriteriaInterface;
use PrestaShopBundle\Translation\TranslatorAwareTrait;

/**
 * Class AttachmentGridDataFactoryDecorator
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
     * @param GridDataFactoryInterface $attachmentDoctrineGridDataFactory
     * @param int $employeeIdLang
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        GridDataFactoryInterface $attachmentDoctrineGridDataFactory,
        $employeeIdLang,
        Connection $connection,
        $dbPrefix
    ) {
        $this->attachmentDoctrineGridDataFactory = $attachmentDoctrineGridDataFactory;
        $this->employeeIdLang = $employeeIdLang;
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
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
    private function applyModifications(RecordCollectionInterface $attachments)
    {
        $modifiedAttachments = [];

        foreach ($attachments as $attachment) {
            if ((int) $attachment['products'] > 0) {
                $attachment['dynamic_message'] = $this->trans(
                    "This file is associated with the following products, do you really want to  delete it?\r\n%products%",
                    ['%products%' => 'testas'],
                    'Admin.Notifications.Warning'
                );
            }

            $modifiedAttachments[] = $attachment;
        }

        return new RecordCollection($modifiedAttachments);
    }
}
