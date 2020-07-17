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

namespace LegacyTests\Unit\Core\Foundation\Database;

use LegacyTests\TestCase\UnitTestCase;
use Phake;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityMetaData;
use PrestaShop\PrestaShop\Core\Foundation\Database\EntityRepository;

class Core_Foundation_Database_EntityRepository_Test extends UnitTestCase
{
    protected function setUp()
    {
        $mockEntityManager = Phake::mock('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\EntityManager');

        $mockDb = Phake::mock('\\PrestaShop\\PrestaShop\\Core\\Foundation\\Database\\DatabaseInterface');

        Phake::when($mockDb)->select(Phake::anyParameters())->thenReturn(array());

        Phake::when($mockEntityManager)->getDatabase()->thenReturn($mockDb);

        $this->repository = new EntityRepository(
            $mockEntityManager,
            'ps_',
            new EntityMetaData()
        );
    }

    public function testCallToInvalidMethodThrowsException()
    {
        $this->expectException(\PrestaShop\PrestaShop\Core\Foundation\Database\Exception::class);

        $this->repository->thisDoesNotExist();
    }

    public function testCallToFindByDoesNotThrow()
    {
        $this->assertInternalType('array', $this->repository->findByStuff('hey'));
    }

    public function testCallToFindOneByDoesNotThrow()
    {
        $this->assertNull($this->repository->findOneByStuff('hey'));
    }
}
