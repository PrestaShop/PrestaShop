<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Module;

use Symfony\Component\Finder\Finder;
use Symfony\Contracts\Service\ResetInterface;

/**
 * Lists the modules customized through the shop `override/modules/` folder.
 *
 * Two override mechanisms are resolved at runtime by the core:
 * - the main module class, from `override/modules/<module>/<module>.php` (see Module::coreLoadModule())
 * - a module front controller, from `override/modules/<module>/controllers/front/<controller>.php` (see Dispatcher::dispatch())
 *
 * Any other PHP file found in a module override folder is reported too: it does not alter the module
 * behaviour on its own, but it means the module has been customized and an update may break it.
 */
final class OverriddenModulesProvider implements ResetInterface
{
    /**
     * Blank guard files PrestaShop generates in every folder, they are never actual overrides.
     */
    private const IGNORED_FILE_NAMES = ['index.php'];

    /**
     * @var array<string, string[]>|null Module technical name => overridden file paths
     */
    private ?array $overriddenFiles = null;

    public function __construct(
        private readonly string $overrideDir,
    ) {
    }

    public function isOverridden(string $moduleName): bool
    {
        return [] !== $this->getOverriddenFiles($moduleName);
    }

    /**
     * The scan is memoized for the request, which is long enough for a web request but not for a
     * worker or a test suite, where override files can be added or removed while the process lives.
     */
    public function reset(): void
    {
        $this->overriddenFiles = null;
    }

    /**
     * @return string[] Overridden file paths, relative to `override/modules/<module>/`
     */
    public function getOverriddenFiles(string $moduleName): array
    {
        return $this->getAllOverriddenFiles()[$moduleName] ?? [];
    }

    /**
     * The override folder is scanned once, so that listing pages displaying hundreds of modules
     * only pay for a single filesystem traversal.
     *
     * @return array<string, string[]> Module technical name => overridden file paths
     */
    public function getAllOverriddenFiles(): array
    {
        if (null === $this->overriddenFiles) {
            $this->overriddenFiles = $this->findOverriddenFiles();
        }

        return $this->overriddenFiles;
    }

    /**
     * @return array<string, string[]>
     */
    private function findOverriddenFiles(): array
    {
        $modulesOverrideDir = rtrim($this->overrideDir, '/\\') . DIRECTORY_SEPARATOR . 'modules';
        if (!is_dir($modulesOverrideDir)) {
            return [];
        }

        $finder = (new Finder())
            ->files()
            ->in($modulesOverrideDir)
            ->name('*.php')
            ->notName(self::IGNORED_FILE_NAMES)
            // An override always sits in a folder named after the module it applies to
            ->depth('>= 1');

        $overriddenFiles = [];
        foreach ($finder as $file) {
            [$moduleName, $overriddenFile] = explode('/', str_replace('\\', '/', $file->getRelativePathname()), 2);
            $overriddenFiles[$moduleName][] = $overriddenFile;
        }

        foreach ($overriddenFiles as &$moduleFiles) {
            sort($moduleFiles);
        }

        ksort($overriddenFiles);

        return $overriddenFiles;
    }
}
