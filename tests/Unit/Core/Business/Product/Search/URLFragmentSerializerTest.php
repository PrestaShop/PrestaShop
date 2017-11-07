<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShop\PrestaShop\Tests\Unit\Core\Product\Search;

use PHPUnit_Framework_Testcase;
use PrestaShop\PrestaShop\Core\Product\Search\URLFragmentSerializer;

class URLFragmentSerializerTest extends PHPUnit_Framework_Testcase
{
    private $serializer;

    public function setUp()
    {
        $this->serializer = new URLFragmentSerializer;
    }

    private function doTest($expected, array $fragment)
    {
        $this->assertEquals($expected, $this->serializer->serialize($fragment));
        $this->assertEquals($fragment, $this->serializer->unserialize($expected));
    }

    public function test_serialize_single_monovalued_fragment()
    {
        $this->doTest('a-b', ['a' => ['b']]);
    }

    public function test_serialize_single_multivalued_fragment()
    {
        $this->doTest('a-b-c', ['a' => ['b', 'c']]);
    }

    public function test_serialize_multiple_multivalued_fragments()
    {
        $this->doTest('a-b-c/x-y-z', ['a' => ['b', 'c'], 'x' => ['y', 'z']]);
    }

    public function test_serialize_single_monovalued_fragment_with_dash_in_name()
    {
        $this->doTest('a-b--c', ['a' => ['b-c']]);
    }
}
