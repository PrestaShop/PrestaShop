<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Customer\Group\Provider;

use Category;
use Db;
use Group as CustomerGroup;
use GroupReduction;
use Module;
use PrestaShop\PrestaShop\Core\Group\Provider\CustomerGroupLegacyDataProviderInterface;

final class CustomerGroupLegacyDataProvider implements CustomerGroupLegacyDataProviderInterface
{
    public function getInstalledModules(): array
    {
        return Module::getModulesInstalled() ?: [];
    }

    public function getAuthorizedModuleIds(int $groupId, array $shopIds): array
    {
        $authorizedModules = Module::getAuthorizedModules($groupId, $shopIds);
        if (!is_array($authorizedModules)) {
            return array_column($this->getInstalledModules(), 'id_module');
        }

        return array_column($authorizedModules, 'id_module');
    }

    public function getCategoryReductions(int $groupId, int $languageId): array
    {
        $rows = GroupReduction::getGroupReductions($groupId, $languageId);
        if (!is_array($rows)) {
            return [];
        }

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id_category' => (int) $row['id_category'],
                'reduction' => (float) $row['reduction'] * 100,
                'name' => $row['name'] ?? '',
            ];
        }

        return $result;
    }

    public function saveCategoryReductions(int $groupId, array $reductions): void
    {
        $db = Db::getInstance();
        $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'group_reduction` WHERE `id_group` = ' . $groupId);
        $db->execute('DELETE FROM `' . _DB_PREFIX_ . 'product_group_reduction_cache` WHERE `id_group` = ' . $groupId);

        foreach ($reductions as $item) {
            $idCategory = (int) ($item['id_category'] ?? 0);
            $reduction = (float) ($item['reduction'] ?? 0);

            if ($idCategory <= 0 || $reduction < 0 || $reduction > 100) {
                continue;
            }

            $category = new Category($idCategory);
            $category->addGroupsIfNoExist($groupId);

            $groupReduction = new GroupReduction();
            $groupReduction->id_group = $groupId;
            $groupReduction->id_category = $idCategory;
            $groupReduction->reduction = $reduction / 100;
            $groupReduction->save();
        }
    }

    public function saveModuleRestrictions(int $groupId, array $authorizedModuleIds, array $shopIds): void
    {
        CustomerGroup::addModulesRestrictions($groupId, $authorizedModuleIds, $shopIds);
    }
}
