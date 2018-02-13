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

namespace Tests\Unit\Core\Foundation\Database;

use Tests\TestCase\UnitTestCase;
use Phake;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityRepository;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityMetaData;

class Core_Foundation_Database_EntityRepository_Test extends UnitTestCase
{
    public function setUp()
    {
        $mockEntityManager = Phake::mock('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\EntityManager');

        $mockDb = Phake::mock('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\DatabaseInterface');

        Phake::when($mockDb)->select(Phake::anyParameters())->thenReturn(array());

        Phake::when($mockEntityManager)->getDatabase()->thenReturn($mockDb);

        $this->repository = new EntityRepository(
            $mockEntityManager,
            'ps_',
            new EntityMetaData
        );
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Foundation\Database\Exception
     */
    public function test_call_to_invalid_method_throws_exception()
    {
        $this->repository->thisDoesNotExist();
    }

    public function test_call_to_findBy_does_not_throw()
    {
        $this->repository->findByStuff('hey');
    }

    public function test_call_to_findOneBy_does_not_throw()
    {
        $this->repository->findOneByStuff('hey');
    }
}
