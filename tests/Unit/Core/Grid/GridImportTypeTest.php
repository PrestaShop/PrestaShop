<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Grid;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Import\Entity;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 * A grid offering an Import action links to the import page with an "import_type" naming the
 * entity to preselect, and ImportConfigFactory resolves that name through Entity::getFromName().
 * A name outside Entity::AVAILABLE_TYPES therefore silently leaves the page on its default
 * entity, which is how the Stores grid ended up pointing at a non-existent "stores" entity.
 */
class GridImportTypeTest extends TestCase
{
    public function testEveryGridImportsAnEntityThatExists(): void
    {
        $importTypes = $this->findImportTypesInGridFactories();

        // Guards against the scan silently matching nothing and passing vacuously.
        $this->assertGreaterThanOrEqual(
            10,
            count($importTypes),
            'Expected the import actions of the grids to be found, the scan matched almost nothing.'
        );

        foreach ($importTypes as $file => $names) {
            foreach ($names as $name) {
                $this->assertArrayHasKey(
                    $name,
                    Entity::AVAILABLE_TYPES,
                    sprintf('%s links to the import page with the unknown entity name "%s".', $file, $name)
                );
            }
        }
    }

    /**
     * @return array<string, string[]> file name => entity names it links with
     */
    private function findImportTypesInGridFactories(): array
    {
        $directory = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator(_PS_ROOT_DIR_ . '/src/Core/Grid')
        );

        $importTypes = [];
        foreach ($directory as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            if (preg_match_all(
                "/'import_type'\s*=>\s*'([^']*)'/",
                (string) file_get_contents($file->getPathname()),
                $matches
            )) {
                $importTypes[$file->getFilename()] = $matches[1];
            }
        }

        return $importTypes;
    }
}
