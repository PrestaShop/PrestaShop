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

/**
 * Simple class to output CSV data
 * Uses CollectionCore.
 *
 * @since 1.5
 */
class CSVCore
{
    public $filename;
    public $collection;
    public $delimiter;

    /**
     * Loads objects, filename and optionnaly a delimiter.
     *
     * @param array|Iterator $collection Collection of objects / arrays (of non-objects)
     * @param string $filename used later to save the file
     * @param string $delimiter delimiter used
     */
    public function __construct($collection, $filename, $delimiter = ';')
    {
        $this->filename = $filename;
        $this->delimiter = $delimiter;
        $this->collection = $collection;
    }

    /**
     * Main function
     * Adds headers
     * Outputs.
     */
    public function export()
    {
        $this->headers();

        $headerLine = false;

        foreach ($this->collection as $object) {
            $vars = get_object_vars($object);
            if (!$headerLine) {
                $this->output(array_keys($vars));
                $headerLine = true;
            }

            // outputs values
            $this->output($vars);
            unset($vars);
        }
    }

    /**
     * Wraps data and echoes
     * Uses defined delimiter.
     *
     * @param array $data
     */
    public function output($data)
    {
        $wrappedData = array_map(array('CSVCore', 'wrap'), $data);
        echo sprintf("%s\n", implode($this->delimiter, $wrappedData));
    }

    /**
     * Escapes data.
     *
     * @param string $data
     *
     * @return string $data
     */
    public static function wrap($data)
    {
        $data = str_replace(array('"', ';'), '', $data);

        return sprintf('"%s"', $data);
    }

    /**
     * Adds headers.
     */
    public function headers()
    {
        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="' . $this->filename . '.csv"');
    }
}
