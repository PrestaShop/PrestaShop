<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Module;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Module\Module;

/**
 * A module updated by hand - a git clone, an upload over FTP - is ahead on disk while the database
 * still holds the version of the last upgrade that ran. Asking whether the API has something newer
 * has to compare against the files, because the files are what a download would replace. Comparing
 * against the database made an older package from the API look like an update, and clicking it
 * reverted the manual work.
 */
class ModuleNewVersionAvailableTest extends TestCase
{
    /**
     * @dataProvider getVersions
     */
    public function testTheApiIsOnlyNewerThanWhatIsOnDisk(
        string $databaseVersion,
        string $diskVersion,
        $apiVersion,
        bool $expected,
        string $because
    ): void {
        $module = new Module(
            ['version' => $apiVersion],
            ['version' => $diskVersion],
            ['version' => $databaseVersion, 'installed' => 1]
        );
        $module->attributes->set('version_available', $apiVersion);

        self::assertSame($expected, $module->hasNewVersionAvailable(), $because);
    }

    public static function getVersions(): array
    {
        return [
            'manually updated past what the API offers' => [
                '1.0.0', '2.0.0', '1.5.0', false,
                'downloading 1.5.0 over the 2.0.0 on disk would undo the manual update',
            ],
            'the API genuinely has a newer package' => [
                '1.0.0', '1.0.0', '1.5.0', true,
                'nothing local is newer, so the API package is an update',
            ],
            'files updated by hand, API not ahead of them' => [
                '1.0.0', '1.5.0', '1.5.0', false,
                'the API has nothing the files do not already have',
            ],
            'no package offered at all' => [
                '1.0.0', '1.0.0', 0, false,
                'there is nothing to download',
            ],
        ];
    }
}
