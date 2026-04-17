<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Simple class to output CSV data
 * Uses CollectionCore.
 */
class CSVCore
{
    public $filename;
    public $collection;
    public $delimiter;

    /**
     * Loads objects, filename and optionally a delimiter.
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
        $wrappedData = array_map(['CSVCore', 'wrap'], $data);
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
        $data = str_replace(['"', ';'], '', $data);

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
