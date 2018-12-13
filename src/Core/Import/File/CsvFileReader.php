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

namespace PrestaShop\PrestaShop\Core\Import\File;

use PrestaShop\PrestaShop\Core\Import\Exception\UnreadableFileException;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\DataRow;
use SplFileInfo;

/**
 * Class CsvFileReader defines a CSV file reader.
 */
final class CsvFileReader implements FileReaderInterface
{
    /**
     * @var string the data delimiter in the CSV row
     */
    private $delimiter;

    /**
     * @var int
     */
    private $length;

    /**
     * @var string
     */
    private $enclosure;

    /**
     * @var string
     */
    private $escape;

    /**
     * @param string $delimiter
     * @param int $length
     * @param string $enclosure
     * @param string $escape
     */
    public function __construct($delimiter = ';', $length = 0, $enclosure = '"', $escape = '\\')
    {
        $this->delimiter = $delimiter;
        $this->length = $length;
        $this->enclosure = $enclosure;
        $this->escape = $escape;
    }

    /**
     * {@inheritdoc}
     */
    public function read(SplFileInfo $file)
    {
        if (!$file->isReadable()) {
            throw new UnreadableFileException();
        }

        $handle = fopen($file->getPathname(), 'r');

        if (false === $handle) {
            throw new UnreadableFileException();
        }

        while ($row = fgetcsv($handle, $this->length, $this->delimiter, $this->enclosure, $this->escape)) {
            yield DataRow::createFromArray($row);
        }

        fclose($handle);
    }
}
