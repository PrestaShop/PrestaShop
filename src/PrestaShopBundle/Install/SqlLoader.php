<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Install;

use Db;
use PrestashopInstallerException;

class SqlLoader
{
    /**
     * @var Db
     */
    protected $db;

    /**
     * @var array List of keywords which will be replaced in queries
     */
    protected $metadata = [];

    /**
     * @var array List of errors during last parsing
     */
    protected $errors = [];

    /**
     * @param Db|null $db
     */
    public function __construct(?Db $db = null)
    {
        if (null === $db) {
            $db = Db::getInstance();
        }
        $this->db = $db;
    }

    /**
     * Set a list of keywords which will be replaced in queries.
     *
     * @param array $data
     */
    public function setMetaData(array $data)
    {
        foreach ($data as $k => $v) {
            $this->metadata[$k] = $v;
        }
    }

    /**
     * Parse a SQL file and execute queries.
     *
     * @deprecated use parseFile()
     *
     * @param string $filename
     * @param bool $stop_when_fail
     */
    public function parse_file($filename, $stop_when_fail = true)
    {
        return $this->parseFile($filename, $stop_when_fail);
    }

    /**
     * Parse a SQL file and execute queries.
     *
     * @param string $filename
     * @param bool $stop_when_fail
     */
    public function parseFile($filename, $stop_when_fail = true)
    {
        if (!file_exists($filename)) {
            throw new PrestashopInstallerException("File $filename not found");
        }

        return $this->parse(file_get_contents($filename), $stop_when_fail);
    }

    /**
     * Parse and execute a list of SQL queries.
     *
     * @param string $content
     * @param bool $stop_when_fail
     */
    public function parse($content, $stop_when_fail = true)
    {
        $this->errors = [];

        $content = str_replace(array_keys($this->metadata), array_values($this->metadata), $content);
        $queries = preg_split('#;\s*[\r\n]+#', $content);
        foreach ($queries as $query) {
            $query = trim($query);
            if (!$query) {
                continue;
            }

            if (!$this->db->execute($query)) {
                $this->errors[] = [
                    'errno' => $this->db->getNumberError(),
                    'error' => $this->db->getMsgError(),
                    'query' => $query,
                ];

                if ($stop_when_fail) {
                    return false;
                }
            }
        }

        return count($this->errors) ? false : true;
    }

    /**
     * Get list of errors from last parsing.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
