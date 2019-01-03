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
use PrestaShopBundle\Service\Mail\MailTemplate;
use PrestaShopBundle\Service\Mail\MailTemplateCatalogInterface;
use PrestaShopBundle\Service\Mail\MailTemplateCollection;
use PrestaShopBundle\Service\Mail\MailTemplateCollectionInterface;
use PrestaShopBundle\Service\Mail\MailTemplateGenerator;
use PrestaShopBundle\Service\Mail\MailTemplateInterface;
use PrestaShopBundle\Service\Mail\MailTemplateRenderer;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class MailTemplateGeneratorTest extends TestCase
{
    /** @var string */
    private $tempDir;

    /** @var Filesystem */
    private $fs;

    /** @var MailTemplateCollectionInterface */
    private $templates;

    public function setUp()
    {
        parent::setUp();
        $this->fs = new Filesystem();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mail_templates';
        $this->fs->remove($this->tempDir);
        $this->templates = new MailTemplateCollection([
            new MailTemplate(
                'classic',
                MailTemplateInterface::CORE_TEMPLATES,
                'account.html',
                implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'core', 'account.html.twig'])
            ),
            new MailTemplate(
                'classic',
                MailTemplateInterface::CORE_TEMPLATES,
                'account.txt',
                implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'core', 'account.txt.twig'])
            ),
            new MailTemplate(
                'classic',
                MailTemplateInterface::MODULES_TEMPLATES,
                'followup_1',
                implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'modules', 'followup', 'followup_1.twig'])
            ),
            new MailTemplate(
                'classic',
                MailTemplateInterface::MODULES_TEMPLATES,
                'productoutofstock',
                implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'modules', 'ps_emailalerts', 'productoutofstock.twig'])
            ),
        ]);
        $this->fs->mkdir($this->tempDir);
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

    public function testGenerateTemplates()
    {
        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(['classic'], $this->templates),
            $this->createRendererMock()
        );
        $this->assertNotNull($generator);

        $generator->generateThemeTemplates('classic', $this->tempDir);
        $expectedFiles = [
            'account.html' => 'account.html_',
            'account.txt' => 'account.txt_',
            'followup_1' => 'followup_1_',
            'productoutofstock' => 'productoutofstock_',
        ];
        $this->checkExpectedFiles($expectedFiles);
    }

    public function testGenerateTemplatesWithLocale()
    {
        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(['classic'], $this->templates),
            $this->createRendererMock()
        );
        $this->assertNotNull($generator);

        $generator->generateThemeTemplates('classic', $this->tempDir, 'fr');
        $expectedFiles = [
            'account.html' => 'account.html_fr',
            'account.txt' => 'account.txt_fr',
            'followup_1' => 'followup_1_fr',
            'productoutofstock' => 'productoutofstock_fr',
        ];
        $this->checkExpectedFiles($expectedFiles);
    }

    /**
     * @param array $expectedFiles
     */
    private function checkExpectedFiles(array $expectedFiles)
    {
        $finder = new Finder();
        $finder->files()->in($this->tempDir)->depth(0);
        $this->assertCount(count($expectedFiles), $finder);
        foreach ($expectedFiles as $expectedFile => $expectedContent) {
            $filePath = implode(DIRECTORY_SEPARATOR, [$this->tempDir, $expectedFile]);
            $this->assertTrue($this->fs->exists($filePath));
            $this->assertEquals($expectedContent, file_get_contents($filePath));
        }
    }

    /**
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateRenderer
     */
    private function createRendererMock()
    {
        $renderer = $this->getMockBuilder(MailTemplateRenderer::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $renderer
            ->expects($this->atLeastOnce())
            ->method('render')
            ->will($this->returnCallback(function(MailTemplateInterface $template, $locale) {
                return implode('_', [$template->getName(), $locale]);
            }))
        ;

        return $renderer;
    }

    /**
     * @param array $availableThemes
     * @param MailTemplateCollectionInterface|null $collection
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateCatalogInterface
     */
    private function createCatalogMock($availableThemes = [], MailTemplateCollectionInterface $collection = null)
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

        if (null !== $collection) {
            $catalogMock
                ->expects($this->once())
                ->method('listTemplates')
                ->with($this->equalTo('classic'))
                ->willReturn($collection)
            ;
        }

        return $catalogMock;
    }
}
