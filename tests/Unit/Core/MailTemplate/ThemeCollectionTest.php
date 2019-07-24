<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
