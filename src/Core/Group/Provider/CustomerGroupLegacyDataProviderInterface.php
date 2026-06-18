<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Group\Provider;

interface CustomerGroupLegacyDataProviderInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getInstalledModules(): array;

    /**
     * @param array<int> $shopIds
     *
     * @return array<int>
     */
    public function getAuthorizedModuleIds(int $groupId, array $shopIds): array;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getCategoryReductions(int $groupId, int $languageId): array;

    /**
     * @param array<array<string, mixed>> $reductions Each entry: ['id_category' => int, 'reduction' => float]
     */
    public function saveCategoryReductions(int $groupId, array $reductions): void;

    /**
     * @param array<int> $authorizedModuleIds
     * @param array<int> $shopIds
     */
    public function saveModuleRestrictions(int $groupId, array $authorizedModuleIds, array $shopIds): void;
}
