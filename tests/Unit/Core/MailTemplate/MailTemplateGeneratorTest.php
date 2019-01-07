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

namespace Tests\Unit\Core\MailTemplate;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Exception\InvalidException;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplate;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateCollection;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateGenerator;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateRendererInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Language;

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
                MailTemplateInterface::CORE_CATEGORY,
                MailTemplateInterface::HTML_TYPE,
                'account',
                implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'core', 'account.html.twig'])
            ),
            new MailTemplate(
                'classic',
                MailTemplateInterface::CORE_CATEGORY,
                MailTemplateInterface::RAW_TYPE,
                'account',
                implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'core', 'account.txt.twig'])
            ),
            new MailTemplate(
                'classic',
                MailTemplateInterface::MODULES_CATEGORY,
                MailTemplateInterface::HTML_TYPE,
                'followup_1',
                implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'modules', 'followup', 'followup_1.twig'])
            ),
            new MailTemplate(
                'classic',
                MailTemplateInterface::MODULES_CATEGORY,
                MailTemplateInterface::RAW_TYPE,
                'productoutofstock',
                implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'modules', 'ps_emailalerts', 'productoutofstock.twig'])
            ),
        ]);
        $this->fs->mkdir($this->tempDir);
    }

    public function testConstructor()
    {
        $mailRenderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $catalogMock = $this->getMockBuilder(MailTemplateCatalogInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator(
            $catalogMock,
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
        $mailRenderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $generator = new MailTemplateGenerator(
            $this->createCatalogMock(['titi', 'tata']),
            $mailRenderer
        );
        $this->assertNotNull($generator);

        $generator->generateThemeTemplates('toto', $this->createLanguageMock(), sys_get_temp_dir());
    }

    public function testInvalidOutputFolder()
    {
        $mailRenderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
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
            $generator->generateThemeTemplates('toto', $this->createLanguageMock(), $fakeFolder);
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

        $generator->generateThemeTemplates('classic', $this->createLanguageMock(), $this->tempDir);
        $expectedFiles = [
            'account.html' => 'account_html_core_',
            'account.txt' => 'account_raw_core_',
            'followup_1.html' => 'followup_1_html_modules_',
            'productoutofstock.txt' => 'productoutofstock_raw_modules_',
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

        $generator->generateThemeTemplates('classic', $this->createLanguageMock('fr'), $this->tempDir);
        $expectedFiles = [
            'account.html' => 'account_html_core_fr',
            'account.txt' => 'account_raw_core_fr',
            'followup_1.html' => 'followup_1_html_modules_fr',
            'productoutofstock.txt' => 'productoutofstock_raw_modules_fr',
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
            $this->assertTrue($this->fs->exists($filePath), 'File not found '.$filePath);
            $this->assertEquals($expectedContent, file_get_contents($filePath));
        }
    }

    /**
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|MailTemplateRendererInterface
     */
    private function createRendererMock()
    {
        $renderer = $this->getMockBuilder(MailTemplateRendererInterface::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $renderer
            ->expects($this->atLeastOnce())
            ->method('render')
            ->will($this->returnCallback(function(MailTemplateInterface $template, Language $language) {
                return implode('_', [$template->getName(), $template->getType(), $template->getCategory(), $language->iso_code]);
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

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|Language
     */
    private function createLanguageMock($isoCode = null)
    {
        $languageMock = $this->getMockBuilder(Language::class)
            ->disableOriginalConstructor()
            ->getMock();
        ;

        $languageMock->iso_code = $isoCode;

        return $languageMock;
    }
}
