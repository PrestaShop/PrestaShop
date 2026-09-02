<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Employee;

use Doctrine\DBAL\Connection;

/**
 * Class EmployeeLanguageUpdater updates the `id_lang` field in the `employee` table for all employees
 * using a deleted language, assigning them the default language instead.
 */
final class EmployeeLanguageUpdater
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $dbPrefix;

    private $langDefaultId;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     * @param int $langDefaultId
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix,
        int $langDefaultId
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->langDefaultId = $langDefaultId;
    }

    public function replaceDeletedLanguage(int $deletedLangId)
    {
        $this->connection->update(
            $this->dbPrefix . 'employee',
            ['id_lang' => $this->langDefaultId],
            ['id_lang' => $deletedLangId]
        );
    }
}
