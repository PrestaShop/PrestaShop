<?php
namespace PrestaShop\PrestaShop\Tests\Unit;

use Prophecy\Prophet;
use PHPUnit_Framework_TestCase;

class UnitTestCase extends PHPUnit_Framework_TestCase {
    /**
     * @var \Prophecy\Prophet
     */
    protected $prophet;

    /**
     * @var \Symfony\Component\BrowserKit\Client
     */
    protected $client;

    public function setUp()
    {
        parent::setUp();

        $this->prophet = new Prophet();
    }

    public function tearDown()
    {
        $this->prophet->checkPredictions();

        parent::tearDown();
    }

}