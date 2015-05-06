<?php

namespace PrestaShop\PrestaShop\Tests\Unit\Core\Foundation\Database;

use PrestaShop\PrestaShop\Tests\TestCase\UnitTestCase;

use Phake;

use Core_Foundation_Database_EntityRepository;
use Core_Foundation_Database_EntityMetaData;

class Core_Foundation_Database_EntityRepository_Test extends UnitTestCase
{
    public function setup()
    {
        $mockEntityManager = Phake::mock('Core_Foundation_Database_EntityManager');

        $mockDb = Phake::mock('Core_Foundation_Database_Database');

        Phake::when($mockDb)->select(Phake::anyParameters())->thenReturn(array());

        Phake::when($mockEntityManager)->getDatabase()->thenReturn($mockDb);

        $this->repository = new Core_Foundation_Database_EntityRepository(
            $mockEntityManager,
            'ps_',
            new Core_Foundation_Database_EntityMetaData
        );
    }

    /**
     * @expectedException Exception
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
