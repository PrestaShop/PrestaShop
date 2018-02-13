<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShopBundle\Component;

use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvResponse extends StreamedResponse
{
    // Mode used to paginate page per page, 1/100, 2/100, 3/000, etc
    const MODE_PAGINATION = 1;

    // Mode used to paginate by offset, 1/100, 100/100, 200/100, etc (like MySql limit)
    const MODE_OFFSET = 2;

    /**
     * @var array() CSV content
     */
    private $data;

    /**
     * @var String Export filename
     */
    private $fileName;

    /**
     * @var array
     */
    private $headersData = array();

    /**
     * @var int, self::MODE_PAGINATION by default
     */
    private $modeType = self::MODE_PAGINATION;

    /**
     * @var null|int
     */
    private $start = null;

    /**
     * @var int Default limit
     */
    private $limit = 1000;

    /**
     * Constructor.
     *
     * @param callable|null $callback A valid PHP callback or null to set it later
     * @param int           $status   The response status code
     * @param array         $headers  An array of response headers
     */
    public function __construct($callback = null, $status = 200, $headers = array())
    {
        parent::__construct($callback, $status, $headers);

        if (is_null($callback)) {
            $this->setCallback(array($this, 'processData'));
        }

        $this->setFileName('export_' . date('Y-m-d_His') . '.csv');
        $this->headers->set('Content-Type', 'text/csv; charset=utf-8');
    }

    /**
     * @param array|callable $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param array $headersData
     * @return $this
     */
    public function setHeadersData(array $headersData)
    {
        $this->headersData = $headersData;

        return $this;
    }

    /**
     * @param $modeType int
     * @return $this
     */
    public function setModeType($modeType)
    {
        $this->modeType = (int) $modeType;

        return $this;
    }

    /**
     * @param $start int
     * @return $this
     */
    public function setStart($start)
    {
        $this->start = (int) $start;

        return $this;
    }


    /**
     * @param $limit int
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * @param String $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        $disposition = $this->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $this->fileName
        );
        $this->headers->set('Content-Disposition', $disposition);

        return $this;
    }

    /**
     * Callback function for StreamedResponse
     * @throws \LogicException
     */
    public function processData()
    {
        $this->initStart();

        if (is_array($this->data)) {
            $this->processDataArray();

            return;
        }

        if (is_callable($this->data)) {
            $this->processDataCallback();

            return;
        }

        throw new \LogicException('The data must be an array or a valid PHP callable function.');
    }

    /**
     * Process to data export if $this->data is an array
     */
    private function processDataArray()
    {
        $handle = tmpfile();
        fputcsv($handle, $this->headersData, ';');

        foreach ($this->data as $line) {
            fputcsv($handle, $line, ';');
        }

        $this->dumpFile($handle);
    }

    /**
     * Process to data export if $this->data is a callable function
     */
    private function processDataCallback()
    {
        $handle = tmpfile();
        fputcsv($handle, $this->headersData, ';');

        do {
            $data = call_user_func_array($this->data, array($this->start, $this->limit));

            $count = count($data);
            if ($count === 0) {
                break;
            }

            foreach ($data as $line) {
                $lineData = array();

                foreach (array_keys($this->headersData) as $column) {
                    if (array_key_exists($column, $line)) {
                        $lineData[] = $line[$column];
                    }
                }

                fputcsv($handle, $lineData, ';');
            }

            $this->incrementData();

        } while ($count === $this->limit);

        $this->dumpFile($handle);
    }

    /**
     * Just init $this->start if it is null
     */
    private function initStart()
    {
        if (null !== $this->start) {
            return;
        }

        if (self::MODE_PAGINATION === $this->modeType) {
            $this->setStart(1);
        }

        if (self::MODE_OFFSET === $this->modeType) {
            $this->setStart(0);
        }
    }

    /**
     * Increment the start data for the process
     * @throws \LogicException
     */
    private function incrementData()
    {
        if (self::MODE_PAGINATION === $this->modeType) {
            $this->setStart($this->start + 1);

            return;
        }

        if (self::MODE_OFFSET === $this->modeType) {
            $this->setStart($this->start + $this->limit);

            return;
        }

        throw new \LogicException('The modeType is not a valid value.');
    }

    /**
     * @param $handle, file pointer
     */
    private function dumpFile($handle)
    {
        fseek($handle, 0);

        while (!feof($handle)) {
            $buffer = fread($handle, 1024);
            echo $buffer;
            flush();
        }

        fclose($handle);
    }
}
