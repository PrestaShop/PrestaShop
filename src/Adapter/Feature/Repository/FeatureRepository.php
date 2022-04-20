<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Feature\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use PrestaShop\PrestaShop\Core\Domain\Feature\Exception\FeatureNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Feature\ValueObject\FeatureId;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

/**
 * Methods to access data storage for FeatureValue
 */
class FeatureRepository extends AbstractObjectModelRepository
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var string
     */
    protected $dbPrefix;

    /**
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        Connection $connection,
        string $dbPrefix
    ) {
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
    }

    /**
     * @param FeatureId $featureId
     *
     * @throws FeatureNotFoundException
     * @throws CoreException
     */
    public function assertExists(FeatureId $featureId): void
    {
        $this->assertObjectModelExists(
            (int) $featureId->getValue(),
            'feature',
            FeatureNotFoundException::class
        );
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @param array|null $filters
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFeatures(?int $limit = null, ?int $offset = null, ?array $filters = []): array
    {
        $qb = $this->getFeaturesQueryBuilder($filters)
            ->select('f.*, fl.*')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
        ;

        $results = $qb->execute()->fetchAll();
        $localizedNames = [];
        $featuresById = [];
        foreach ($results as $result) {
            $featureId = (int) $result['id_feature'];
            $localizedNames[$featureId][(int) $result['id_lang']] = $result['name'];
            $featuresById[$featureId] = [
                'id_feature' => $featureId,
                'position' => (int) $result['position'],
            ];
        }

        $features = [];
        foreach ($featuresById as $featureById) {
            $features[] = [
                'id_feature' => $featureById['id_feature'],
                'position' => $featureById['position'],
                'localized_names' => $localizedNames[$featureById['id_feature']],
            ];
        }

        return $features;
    }

    /**
     * @param array|null $filters
     *
     * @return int
     */
    public function getFeaturesCount(?array $filters = []): int
    {
        $qb = $this->getFeaturesQueryBuilder($filters)
            ->select('COUNT(f.id_feature_value) AS total_feature_values')
            ->addGroupBy('f.id_feature_value')
        ;

        return (int) $qb->execute()->fetch()['total_feature_values'];
    }

    /**
     * @param array|null $filters
     *
     * @return QueryBuilder
     */
    private function getFeaturesQueryBuilder(?array $filters): QueryBuilder
    {
        //@todo: filters are not handled.
        $qb = $this->connection->createQueryBuilder();
        $qb->from($this->dbPrefix . 'feature', 'f')
            ->leftJoin('f', $this->dbPrefix . 'feature_lang', 'fl', 'fl.id_feature = f.id_feature')
            ->addOrderBy('f.position', 'ASC')
        ;

        return $qb;
    }
}
