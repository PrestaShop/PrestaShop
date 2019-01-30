<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace LegacyTests\Unit\Core\Product\Search;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Product\Search\URLFragmentSerializer;

class URLFragmentSerializerTest extends Testcase
{
    private $serializer;

    protected function setUp()
    {
        $this->serializer = new URLFragmentSerializer();
    }

    private function doTest($expected, array $fragment)
    {
        $this->assertEquals($expected, $this->serializer->serialize($fragment));
        $this->assertEquals($fragment, $this->serializer->unserialize($expected));
    }

    public function testSerializeSingleMonovaluedFragment()
    {
        $this->doTest('a-b', ['a' => ['b']]);
    }

    public function testSerializeSingleMultivaluedFragment()
    {
        $this->doTest('a-b-c', ['a' => ['b', 'c']]);
    }

    public function testSerializeMultipleMultivaluedFragments()
    {
        $this->doTest('a-b-c/x-y-z', ['a' => ['b', 'c'], 'x' => ['y', 'z']]);
    }

    public function testSerializeSingleMonovaluedFragmentWithDashInName()
    {
        $this->doTest('a-b--c', ['a' => ['b-c']]);
    }
}
