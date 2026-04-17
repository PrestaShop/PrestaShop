<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\MailTemplate\Theme;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCollection;

class ThemeCollectionTest extends TestCase
{
    public function testGetByName()
    {
        $theme1 = new Theme('theme1');
        $theme2 = new Theme('theme2');
        $theme3 = new Theme('theme1');
        $collection = new ThemeCollection([
            $theme1,
            $theme2,
            $theme3,
        ]);
        $this->assertEquals(3, $collection->count());
        $this->assertEquals($theme1, $collection->getByName('theme1'));
        $this->assertEquals($theme2, $collection->getByName('theme2'));
        $this->assertEquals(null, $collection->getByName('theme3'));
    }
}
