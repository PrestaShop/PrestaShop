<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Core\Import\File\DataRow\Factory;

use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRowCollection;
use PrestaShop\PrestaShop\Core\Import\File\FileReaderInterface;
use SplFileInfo;

/**
 * Class DataRowCollectionFactory defines a data row collection factory.
 */
final class DataRowCollectionFactory implements DataRowCollectionFactoryInterface
{
    /**
     * @var FileReaderInterface
     */
    private $fileReader;

    /**
     * @param FileReaderInterface $fileReader
     */
    public function __construct(FileReaderInterface $fileReader)
    {
        $this->fileReader = $fileReader;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFromFile(SplFileInfo $file, $maxRowsInCollection = null)
    {
        $dataRowCollection = new DataRowCollection();
        $rowIndex = 0;

        foreach ($this->fileReader->read($file) as $dataRow) {
            if (null !== $maxRowsInCollection && $rowIndex >= $maxRowsInCollection) {
                break;
            }

            $dataRowCollection->addDataRow($dataRow);
            ++$rowIndex;
        }

        return $dataRowCollection;
    }
}
