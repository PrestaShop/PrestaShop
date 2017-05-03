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
namespace PrestaShopBundle\Service;

/**
 * CSV tools and common features on CSV format import/export
 */
class Csv
{
    /**
     * Exports CSV data with a paginated callback provider.
     *
     * This feature will allow a CSV download without memory limitations.
     * This will send the file through stdout during generation and then there is no size limit except HTTP connection limits.
     * The call will make a PHP die at the end of execution to close the connexion and then the file.
     * Sends specific HTTP headers to force the browser to download the content as a file.
     *
     * Behavior of the callable return:
     * The callable $dataCallback must return an array with a maximum amount of elements inside (<=limit). If the amount of elements returned
     * by the callable is less than the limit parameter (<limit), then we consider that the callable has no more element to extract:
     * it will be considered as the last page result and then the exportData method will finish after this page.
     *
     * Limits of the method:
     * - The exportData works with a internal buffer php://memory and php://temp/maxmemory:XXX to build one page,
     * and then send it through the stdout in live by a 'flush' behavior. Then there is no memory limitation for the total size of the resulting file:
     * the whole generated file is not stored on the PHP server side, but built progressively on the browser side.
     * Tested and worked for a CSV file over 200MB.
     * - The memory limitation will stay on the size of the page: each requested page on the $dataCallback callable is limited in memory by the PHP settings.
     * If the page uses too much memory, then we can reduce the size of the page through the $limitPerPage parameter: More requests on the $dataCallback,
     * but smaller results.
     * - The resulting file is not limited in size (weight) by PHP. It is not limited in row count, but keep in mind that Excel/OpenOffice limitation is still
     * around 1 million rows for one sheet. Tested over 1 million rows: export worked but OpenOffice stopped opening after 1 million rows.
     * - The time limit is the most important limit. You can set it through the $timeLimit parameter, BUT the HTTP server that hosts the application
     * may have its own limitation in time (or in file size).
     * So the maximum amount of data the user will be able to export will be limited by the connection speed and the host limitations.
     *
     * @param callable $dataCallback The callable will take 2 parameters: {offset, limit} and must return 2D indexed array containing data to export in the CSV. The columns will be fetched with the $headers keys.
     * @param array[string:string] $headers An indexed array of column. Keys are used to fetch data in the $dataCallback results, and values are used to fill the first CSV row (CSV headers)
     * @param int $limitPerPage This is the size of a page that will be fetch through the callback call. Reduce this value if there is memory problems
     * @param string $fileName The name of the file that will be downloaded by the browser
     * @param int $timeLimit The time limit to execute the export, in seconds.
     * @param boolean $windowsCRLF True to use windows \ r \ n line break instead of classical \ n.
     */
    public function exportData($dataCallback, $headers, $limitPerPage, $fileName, $timeLimit = 0, $windowsCRLF = false)
    {
        set_time_limit($timeLimit); // Well, you are not limited in time if you want...
        $offset = 0;

        // Prepare headers
        header('Content-type: text/csv');
        header('Content-Type: application/force-download; charset=UTF-8');
        header('Cache-Control: no-store, no-cache');
        header('Content-disposition: attachment; filename="'.$fileName.'"');

        // send file headers line using fputcsv for auto enclosure.
        $fd = fopen('php://memory', 'wb'); // stays in memory, sufficient
        fputcsv($fd, $headers, ';', '"');
        if ($windowsCRLF) {
            fseek($fd, -1, SEEK_CUR); // rewind 1 char to override "\n" put by fputcsv
            fputs($fd, "\r\n"); // Windows line break forced (CRLF)
        }
        rewind($fd); // back to first char of the file
        fpassthru($fd); // flush all content to buffer at once.
        @fclose($fd); // Delete the file from php://temp memory.

        // fetch products page by page and dump them into stdout
        do {
            $data = $dataCallback($offset, $limitPerPage);
            $count = sizeof($data);
            if ($count == 0) {
                break;
            }

            // Write csv file into memory and send it to buffer.
            $fd = fopen('php://temp/maxmemory:'.(1024*1024), 'wb'); // stays in memory, but if exceeds 1MB, then use temporary file on the filesystem.

            foreach ($data as $item) {
                $line = [];
                foreach (array_keys($headers) as $column) {
                    $line[] = array_key_exists($column, $item)? $item[$column] : '';
                }
                fputcsv($fd, $line, ';', '"');
                if ($windowsCRLF) {
                    fseek($fd, -1, SEEK_CUR); // rewind 1 char to override "\n" put by fputcsv
                    fputs($fd, "\r\n"); // Windows line break forced (CRLF)
                }
            }

            rewind($fd); // back to first char of the file
            fpassthru($fd); // flush all content to buffer at once.
            @fclose($fd); // Delete the file from php://temp memory.

            unset($data);
            $offset += $limitPerPage;
        } while ($count == $limitPerPage);

        // die = close connexion: the file is fully sent.
        die;
    }
}
