<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Adapter;

use Tests\TestCase\IntegrationTestCase;
use PrestaShop\PrestaShop\Adapter\Database;

class AdapterDatabaseTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->db = new Database;
    }

    public function test_values_are_escaped_dataProvider()
    {
        return array(
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
