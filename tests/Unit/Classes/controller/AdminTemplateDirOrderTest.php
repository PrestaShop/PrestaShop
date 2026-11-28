<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes\Controller;

use PHPUnit\Framework\TestCase;
use Smarty;

/**
 * The admin registers two template directories and Smarty resolves {include} in registration order,
 * so an override only wins when its directory comes first. The keys have to stay 0 for the theme and
 * 1 for the override, because Tree, TreeToolbar, TreeToolbarButton, HelperUploader, Helper and
 * AdminController itself read them positionally.
 */
class AdminTemplateDirOrderTest extends TestCase
{
    private const THEME_DIR = '/tmp/ps-test-theme';
    private const OVERRIDE_DIR = '/tmp/ps-test-override';

    /**
     * @dataProvider registrationOrders
     */
    public function testKeysStayPutWhicheverOrderIsRegistered(array $dirs, string $expectedFirst): void
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir([]);

        foreach ($dirs as $key => $dir) {
            $smarty->addTemplateDir($dir, $key);
        }

        $registered = $smarty->getTemplateDir();

        $this->assertSame(
            $expectedFirst,
            rtrim((string) reset($registered), '/'),
            'Smarty searches the directories in registration order'
        );
        $this->assertSame(
            self::THEME_DIR,
            rtrim((string) $smarty->getTemplateDir(0), '/'),
            'index 0 must stay the theme directory whatever the order'
        );
        $this->assertSame(
            self::OVERRIDE_DIR,
            rtrim((string) $smarty->getTemplateDir(1), '/'),
            'index 1 must stay the override directory whatever the order'
        );
    }

    public function registrationOrders(): array
    {
        return [
            'overrides enabled: the override directory is searched first' => [
                [1 => self::OVERRIDE_DIR, 0 => self::THEME_DIR],
                self::OVERRIDE_DIR,
            ],
            'overrides disabled: the theme is searched first' => [
                [0 => self::THEME_DIR, 1 => self::OVERRIDE_DIR],
                self::THEME_DIR,
            ],
        ];
    }

    /**
     * Guards the assumption the fix rests on: passing the pair as one array loses the keys, because
     * Smarty appends integer-keyed entries instead of assigning them.
     */
    public function testPassingThePairAsOneArrayWouldLoseTheKeys(): void
    {
        $smarty = new Smarty();
        $smarty->setTemplateDir([1 => self::OVERRIDE_DIR, 0 => self::THEME_DIR]);

        $this->assertSame(
            self::OVERRIDE_DIR,
            rtrim((string) $smarty->getTemplateDir(0), '/'),
            'the array form renumbers, which is why the directories are added one at a time'
        );
    }
}
