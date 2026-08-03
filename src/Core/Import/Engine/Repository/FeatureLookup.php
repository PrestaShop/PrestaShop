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
final class FeatureLookup
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    public function getFeatureIdByName(string $name, int $languageId): ?int
    {
        $featureId = $this->connection->fetchOne(
            'SELECT f.id_feature
            FROM ' . $this->dbPrefix . 'feature f
            INNER JOIN ' . $this->dbPrefix . 'feature_lang fl
                ON fl.id_feature = f.id_feature AND fl.id_lang = :languageId
            WHERE fl.name = :name
            ORDER BY f.id_feature ASC',
            ['languageId' => $languageId, 'name' => $name]
        );

        return false === $featureId ? null : (int) $featureId;
    }

    /**
     * Pre-defined (non custom) value lookup within a feature.
     */
    public function getFeatureValueIdByValue(int $featureId, string $value, int $languageId): ?int
    {
        $featureValueId = $this->connection->fetchOne(
            'SELECT fv.id_feature_value
            FROM ' . $this->dbPrefix . 'feature_value fv
            INNER JOIN ' . $this->dbPrefix . 'feature_value_lang fvl
                ON fvl.id_feature_value = fv.id_feature_value AND fvl.id_lang = :languageId
            WHERE fv.id_feature = :featureId AND fv.custom = 0 AND fvl.value = :value
            ORDER BY fv.id_feature_value ASC',
            ['languageId' => $languageId, 'featureId' => $featureId, 'value' => $value]
        );

        return false === $featureValueId ? null : (int) $featureValueId;
    }
}
