<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Import\Engine\Repository;

use Doctrine\DBAL\Connection;

/**
 * @internal only meant for internal use by the Import engine components,
 *           not to be overridden or decorated
 */
final class LanguageLookup
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    public function getLanguageIdByIsoCode(string $isoCode): ?int
    {
        $languageId = $this->connection->fetchOne(
            'SELECT id_lang FROM ' . $this->dbPrefix . 'lang WHERE iso_code = :isoCode',
            ['isoCode' => $isoCode]
        );

        return false === $languageId ? null : (int) $languageId;
    }

    /**
     * Every installed language id, active or not (legacy Language::getIDs(false)
     * parity — used to duplicate single-language values on entity creation).
     *
     * @return list<int>
     */
    public function getAllLanguageIds(): array
    {
        $languageIds = $this->connection->fetchFirstColumn(
            'SELECT id_lang FROM ' . $this->dbPrefix . 'lang ORDER BY id_lang ASC'
        );

        return array_map('intval', $languageIds);
    }
}
