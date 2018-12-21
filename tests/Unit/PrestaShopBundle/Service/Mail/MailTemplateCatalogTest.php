<?php
/**
 * Created by PhpStorm.
 * User: jo
 * Date: 2018-12-21
 * Time: 17:45
 */

namespace Tests\Unit\PrestaShopBundle\Service\Mail;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Service\Mail\MailTemplateCatalog;

class MailTemplateCatalogTest extends TestCase
{
    private $tempDir;

    public function setUp()
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testConstructor()
    {
        $catalog = new MailTemplateCatalog();
    }
}
