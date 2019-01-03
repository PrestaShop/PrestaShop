<?php
/**
 * 2007-2018 PrestaShop.
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

namespace Tests\Unit\PrestaShopBundle\Service\Mail;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Exception\InvalidException;
use PrestaShopBundle\Service\Mail\MailTemplateCatalogInterface;
use PrestaShopBundle\Service\Mail\MailTemplateGenerator;
use PrestaShopBundle\Service\Mail\MailTemplateRenderer;
use Symfony\Component\Filesystem\Filesystem;

class MailTemplateGeneratorTest extends TestCase
{
    /** @var string */
    private $tempDir;

    /** @var Filesystem */
    private $fs;

    public function setUp()
    {
        parent::setUp();
        $this->fs = new Filesystem();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mail_templates';
        $this->fs->remove($this->tempDir);
    }

    public function testConstructor()
    {
        $mailRenderer = $this->getMockBuilder(MailTemplateRenderer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(),
            $mailRenderer
        );
        $this->assertNotNull($generator);
    }

    /**
     * @expectedException \PrestaShop\PrestaShop\Core\Exception\InvalidException
     * @expectedExceptionMessage Invalid theme used "toto", only available themes are: titi, tata
     */
    public function testInvalidTheme()
    {
        $mailRenderer = $this->getMockBuilder(MailTemplateRenderer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(['titi', 'tata']),
            $mailRenderer
        );
        $this->assertNotNull($generator);

        $generator->generateThemeTemplates('toto', sys_get_temp_dir());
    }

    public function testInvalidOutputFolder()
    {
        $mailRenderer = $this->getMockBuilder(MailTemplateRenderer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(['titi', 'tata', 'toto']),
            $mailRenderer
        );
        $this->assertNotNull($generator);

        $fakeFolder = $this->tempDir . DIRECTORY_SEPARATOR . 'invisible';
        $caughtException = null;
        try {
            $generator->generateThemeTemplates('toto', $fakeFolder);
        } catch (InvalidException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertInstanceOf(InvalidException::class, $caughtException);
        $expectedMessage = sprintf(
            'Invalid output folder "%s"',
            $fakeFolder
        );
        $this->assertEquals($expectedMessage, $caughtException->getMessage());
    }

    /**
     * @param array $availableThemes
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateCatalogInterface
     */
    private function createCatalogMock($availableThemes = [])
    {
        $catalogMock = $this->getMockBuilder(MailTemplateCatalogInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        if (!empty($availableThemes)) {
            $catalogMock
                ->expects($this->once())
                ->method('listThemes')
                ->willReturn($availableThemes)
            ;
        }

        return $catalogMock;
    }
}
