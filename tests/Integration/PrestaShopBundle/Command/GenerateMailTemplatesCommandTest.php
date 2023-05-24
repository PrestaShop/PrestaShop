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

namespace Tests\Integration\PrestaShopBundle\Command;

use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class GenerateMailTemplatesCommandTest extends KernelTestCase
{
    /** @var Filesystem */
    private $fileSystem;

    public function setUp(): void
    {
        parent::setUp();
        $this->fileSystem = new Filesystem();
        self::bootKernel();
    }

    public function testMissingArguments()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Not enough arguments (missing: "theme, locale").');

        $application = new Application(static::$kernel);

        $command = $application->find('prestashop:mail:generate');
        $this->assertNotNull($command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);
    }

    public function testGenerateTemplates()
    {
        $outputFolder = $this->buildOutputFolder();
        $themeInfos = $this->getThemeInfos('classic');

        $application = new Application(static::$kernel);

        $command = $application->find('prestashop:mail:generate');
        $this->assertNotNull($command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'theme' => 'classic',
            'locale' => 'en',
            'coreOutputFolder' => $outputFolder,
        ]);
        $this->assertEquals(0, $commandTester->getStatusCode());

        $finder = new Finder();
        $finder->files()->in($outputFolder);
        //Core files + modules files, each one in html and txt type
        $totalLayoutsNb = ($themeInfos['coreLayoutsNb'] + $themeInfos['modulesLayoutsNb']) * 2;
        $this->assertEquals($totalLayoutsNb, $finder->count());

        $expectedFiles = [];
        foreach ($themeInfos['coreLayouts'] as $coreLayout) {
            $expectedFiles[] = implode(DIRECTORY_SEPARATOR, [$outputFolder, 'en', $coreLayout . '.html']);
            $expectedFiles[] = implode(DIRECTORY_SEPARATOR, [$outputFolder, 'en', $coreLayout . '.txt']);
        }
        foreach ($themeInfos['modulesLayouts'] as $moduleName => $moduleLayouts) {
            foreach ($moduleLayouts as $moduleLayout) {
                $expectedFiles[] = implode(DIRECTORY_SEPARATOR, [$outputFolder, $moduleName, 'mails', 'en', $moduleLayout . '.html']);
                $expectedFiles[] = implode(DIRECTORY_SEPARATOR, [$outputFolder, $moduleName, 'mails', 'en', $moduleLayout . '.txt']);
            }
        }
        $this->assertFilesExist($expectedFiles);
    }

    public function testGenerateTemplatesWithModulesFolder()
    {
        $outputFolder = $this->buildOutputFolder();
        $coreOutputFolder = implode(DIRECTORY_SEPARATOR, [$outputFolder, MailTemplateInterface::CORE_CATEGORY]);
        $modulesOutputFolder = implode(DIRECTORY_SEPARATOR, [$outputFolder, MailTemplateInterface::MODULES_CATEGORY]);
        $this->fileSystem->mkdir([$coreOutputFolder, $modulesOutputFolder]);
        $themeInfos = $this->getThemeInfos('classic');

        $application = new Application(static::$kernel);

        $command = $application->find('prestashop:mail:generate');
        $this->assertNotNull($command);
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'theme' => 'classic',
            'locale' => 'en',
            'coreOutputFolder' => $coreOutputFolder,
            'modulesOutputFolder' => $modulesOutputFolder,
        ]);
        $this->assertEquals(0, $commandTester->getStatusCode());

        $finder = new Finder();
        $finder->files()->in($outputFolder);
        //Core files + modules files, each one in html and txt type
        $totalLayoutsNb = ($themeInfos['coreLayoutsNb'] + $themeInfos['modulesLayoutsNb']) * 2;
        $this->assertEquals($totalLayoutsNb, $finder->count());

        $expectedFiles = [];
        foreach ($themeInfos['coreLayouts'] as $coreLayout) {
            $expectedFiles[] = implode(DIRECTORY_SEPARATOR, [$coreOutputFolder, 'en', $coreLayout . '.html']);
            $expectedFiles[] = implode(DIRECTORY_SEPARATOR, [$coreOutputFolder, 'en', $coreLayout . '.txt']);
        }
        foreach ($themeInfos['modulesLayouts'] as $moduleName => $moduleLayouts) {
            foreach ($moduleLayouts as $moduleLayout) {
                $expectedFiles[] = implode(DIRECTORY_SEPARATOR, [$modulesOutputFolder, $moduleName, 'mails', 'en', $moduleLayout . '.html']);
                $expectedFiles[] = implode(DIRECTORY_SEPARATOR, [$modulesOutputFolder, $moduleName, 'mails', 'en', $moduleLayout . '.txt']);
            }
        }
        $this->assertFilesExist($expectedFiles);
    }

    /**
     * @param array $files
     */
    private function assertFilesExist(array $files)
    {
        foreach ($files as $file) {
            $this->assertTrue(file_exists($file), sprintf('%s file not found', $file));
        }
    }

    /**
     * @param string $theme
     *
     * @return array
     */
    private function getThemeInfos($theme)
    {
        $themeInfos = [
            'coreLayouts' => [],
            'coreLayoutsNb' => '0',
            'modulesLayouts' => [],
            'modulesLayoutsNb' => '0',
        ];
        $container = static::$kernel->getContainer();
        $mailThemesFolder = (string) $container->getParameter('mail_themes_dir');
        $themeFolder = implode(DIRECTORY_SEPARATOR, [$mailThemesFolder, $theme]);
        $coreFolder = implode(DIRECTORY_SEPARATOR, [$themeFolder, MailTemplateInterface::CORE_CATEGORY]);
        $modulesFolder = implode(DIRECTORY_SEPARATOR, [$themeFolder, MailTemplateInterface::MODULES_CATEGORY]);

        $finder = new Finder();
        $finder->in($coreFolder);
        /** @var \SplFileInfo $coreFile */
        foreach ($finder as $coreFile) {
            $themeInfos['coreLayouts'][] = $coreFile->getBasename('.html.twig');
            ++$themeInfos['coreLayoutsNb'];
        }

        $finder = new Finder();
        $finder->in($modulesFolder)->depth(0);
        /** @var \SplFileInfo $moduleFolder */
        foreach ($finder as $moduleFolder) {
            $themeInfos['modulesLayouts'][$moduleFolder->getBasename()] = [];
            $moduleFinder = new Finder();
            $moduleFinder->in($moduleFolder->getRealPath());
            /** @var \SplFileInfo $moduleFile */
            foreach ($moduleFinder as $moduleFile) {
                $themeInfos['modulesLayouts'][$moduleFolder->getBasename()][] = $moduleFile->getBasename('.html.twig');
                ++$themeInfos['modulesLayoutsNb'];
            }
        }

        return $themeInfos;
    }

    /**
     * @return string
     */
    private function buildOutputFolder()
    {
        $outputFolder = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'mail_templates']);

        $this->fileSystem->remove($outputFolder);
        $this->fileSystem->mkdir($outputFolder);

        return $outputFolder;
    }
}
