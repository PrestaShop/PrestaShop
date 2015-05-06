<?php

namespace PrestaShop\PrestaShop\Tests\Integration\Adapter;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;

use Adapter_Database;

class Adapter_Database_Test extends IntegrationTestCase
{
    public function setup()
    {
        $this->db = new Adapter_Database;
    }

    public function test_values_are_escaped_dataProvider()
    {
        return array (
            array( 'hello'       , 'hello'    ),
            array( '\\\'inject'  , '\'inject' ),
            array( '\\"inject'   , '"inject'  ),
            array( 42            , 42         ),
            array( 4.2           , 4.2        ),
            array( '4\\\'200'    , '4\'200'   ),
        );
    }

    /**
     * @dataProvider test_values_are_escaped_dataProvider
     */
    public function test_values_are_escaped($expectedSanitizedValue, $unsafeInput)
    {
        $this->assertEquals($expectedSanitizedValue, $this->db->escape($unsafeInput));
    }
}
