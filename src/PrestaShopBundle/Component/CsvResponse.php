<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Component;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvResponse extends StreamedResponse
{
    /**
     * @var array() CSV content
     */
    private $data;

    /**
     * @var String Export filename
     */
    private $fileName;

    /**
     * Constructor.
     *
     * @param callable|null $callback A valid PHP callback or null to set it later
     * @param int           $status   The response status code
     * @param array         $headers  An array of response headers
     */
    public function __construct($callback = null, $status = 200, $headers = array())
    {
        parent::__construct(null, $status, $headers);

        if (is_null($callback)) {
            $this->setCallback(array($this, 'processData'));
        }

        $this->headers->set('Content-Type', 'text/csv; charset=utf-8');

        $this->setFileName('export.csv');
    }

    /**
     * @param $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        $this->headers->set('Content-Disposition', 'attachment; filename="'.$this->fileName.'"');

        return $this;
    }

    /**
     * Callback function for StreamedResponse
     */
    public function processData()
    {
        $handle = fopen('php://output', 'w+');

        foreach ($this->data as $line) {
            fputcsv($handle, $line, ';');
        }

        fclose($handle);
    }
}
