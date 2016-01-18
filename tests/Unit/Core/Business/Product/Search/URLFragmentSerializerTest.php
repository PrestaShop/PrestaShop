<?php

namespace PrestaShop\PrestaShop\tests\Unit\Core\Product\Search;

use PHPUnit_Framework_Testcase;
use PrestaShop\PrestaShop\Core\Product\Search\URLFragmentSerializer;

class URLFragmentSerializerTest extends PHPUnit_Framework_Testcase
{
    private $serializer;

    public function setup()
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
