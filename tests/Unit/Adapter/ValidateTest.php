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

namespace Tests\Unit\Adapter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Validate;

class ValidateTest extends TestCase
{
    /** @var Validate */
    private $validate;

    public function setUp()
    {
        $this->validate = new Validate();
    }

    /**
     * @dataProvider getUrls
     *
     * @param string $url
     * @param bool $expected
     */
    public function testValidateIsUrl(string $url, bool $expected)
    {
        $this->assertEquals($expected, $this->validate->isURL($url));
    }

    public function getUrls()
    {
        return [
            ['https://prestashop.com', true],
            ['http://prestashop.com/', true],
            ['https://prestashop.com/demo', true],
            ['http://presat+shop.com/demo', true],
            ['http://presat!shop.com/demo', false],
            ['http://presatshop.com/demo+dev', true],
            ['http://presat!shop.com/demo!dev', false],
            ['presat+shop.com/demo', true],
            ['presat!shop.com/demo', false],
            ['presatshop.com/demo+dev', true],
            ['presat!shop.com/demo!dev', false],
        ];
    }
}
