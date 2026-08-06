<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Hook\FormDataProvider;

use Doctrine\DBAL\Connection;
use Module;
use PrestaShop\PrestaShop\Core\Domain\Hook\Exception\CannotUpdateHookException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Loads existing hook-module registration data for the edit form.
 */
class HookModuleFormDataProvider
{
    public function __construct(
        private readonly Connection $connection,
        #[Autowire('%database_prefix%')]
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * Returns form-compatible data for the edit "Hook a module" form.
     *
     * @return array{id_module: int, id_hook: int, id_hook_original: int, exceptions: string}
     *
     * @throws CannotUpdateHookException
     */
    public function getData(int $hookId, int $moduleId): array
    {
        $module = Module::getInstanceById($moduleId);
        if (!$module) {
            throw new CannotUpdateHookException(sprintf('Module with id "%d" cannot be loaded.', $moduleId));
        }

        $sql = sprintf(
            'SELECT DISTINCT file_name FROM `%shook_module_exceptions` WHERE id_module = :moduleId AND id_hook = :hookId',
            $this->dbPrefix
        );
        $rows = $this->connection->fetchAllAssociative($sql, [
            'moduleId' => $moduleId,
            'hookId' => $hookId,
        ]);

        return [
            'id_module' => $moduleId,
            'id_hook' => $hookId,
            'id_hook_original' => $hookId,
            'exceptions' => implode(', ', array_column($rows, 'file_name')),
        ];
    }
}
