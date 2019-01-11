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
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateFolderCatalog;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateInterface;
use Symfony\Component\Filesystem\Filesystem;

class MailTemplateFolderCatalogTest extends TestCase
{
    /**
     * @var string
     */
    private $tempDir;

    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var array
     */
    private $expectedThemes;

    /**
     * @var array
     */
    private $coreTemplates;

    /**
     * @var array
     */
    private $moduleTemplates;

    public function setUp()
    {
        parent::setUp();
        $this->tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'mail_templates';
        $this->fs = new Filesystem();
        $this->fs->remove($this->tempDir);
        $this->createThemesFiles();
    }

    public function testConstructor()
    {
        $catalog = new MailTemplateFolderCatalog($this->tempDir);
        $this->assertNotNull($catalog);
    }

    public function testListThemes()
    {
        $catalog = new MailTemplateFolderCatalog($this->tempDir);
        $listedThemes = $catalog->listThemes();
        $this->assertEquals($this->expectedThemes, $listedThemes);
    }

    public function testListThemesWithoutThemesFolder()
    {
        $catalog = new MailTemplateFolderCatalog(implode(
            DIRECTORY_SEPARATOR,
            [$this->tempDir, 'invisible']
        ));
        $this->assertNotNull($catalog);

        $caughtException = null;
        try {
            $catalog->listThemes();
        } catch (InvalidException $e) {
            $caughtException = $e;
        }
        $this->assertNotNull($caughtException);
        $this->assertContains('Invalid mail themes folder', $caughtException->getMessage());
        $this->assertContains(': no such directory', $caughtException->getMessage());
    }

    public function testListTemplates()
    {
        $catalog = new MailTemplateFolderCatalog($this->tempDir);
        $templateCollection = $catalog->listTemplates('classic');
        $this->assertNotNull($templateCollection);
        $this->assertInstanceOf(MailTemplateCollectionInterface::class, $templateCollection);
        $this->assertEquals(8, $templateCollection->count());

        //Check core templates
        $coreTemplates = $this->filterTemplatesByCategory($templateCollection, MailTemplateInterface::CORE_CATEGORY);
        $this->assertCount(4, $coreTemplates);

        /** @var MailTemplateInterface $template */
        $template = $coreTemplates[0];
        $this->assertInstanceOf(MailTemplateInterface::class, $template);
        $coreFolder = implode(DIRECTORY_SEPARATOR, [
            realpath($this->tempDir),
            'classic',
            MailTemplateInterface::CORE_CATEGORY,
        ]);
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, [$coreFolder, 'account.html.twig']), $template->getHtmlPath());
        $this->assertEquals(implode(DIRECTORY_SEPARATOR, [$coreFolder, 'account.txt.twig']), $template->getTxtPath());
        $this->assertEquals('classic', $template->getTheme());
        $this->assertEquals('account', $template->getName());
        $this->assertEquals(MailTemplateInterface::CORE_CATEGORY, $template->getCategory());
        $this->assertNull($template->getModuleName());

        //Check module templates
        $modulesTemplates = $this->filterTemplatesByCategory($templateCollection, MailTemplateInterface::MODULES_CATEGORY);
        $this->assertCount(4, $modulesTemplates);

        $moduleTemplatesCount = [];
        /** @var MailTemplateInterface $moduleTemplate */
        foreach ($modulesTemplates as $moduleTemplate) {
            $this->assertEquals('classic', $template->getTheme());
            $this->assertEquals(MailTemplateInterface::MODULES_CATEGORY, $moduleTemplate->getCategory());
            $this->assertNotNull($moduleTemplate->getModuleName());
            if (!isset($moduleTemplatesCount[$moduleTemplate->getModuleName()])) {
                $moduleTemplatesCount[$moduleTemplate->getModuleName()] = [$moduleTemplate->getName()];
            } else {
                $moduleTemplatesCount[$moduleTemplate->getModuleName()][] = $moduleTemplate->getName();
            }
        }
        $this->assertCount(2, $moduleTemplatesCount['followup']);
        $this->assertEquals(['followup_1', 'followup_2'], $moduleTemplatesCount['followup']);
        $this->assertCount(2, $moduleTemplatesCount['ps_emailalerts']);
        $this->assertEquals(['productoutofstock', 'return_slip'], $moduleTemplatesCount['ps_emailalerts']);
    }

    public function testListTemplatesWithoutCoreFolder()
    {
        $catalog = new MailTemplateFolderCatalog($this->tempDir);
        //No bug occurs if the folder does not exist
        $this->fs->remove(implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'classic', MailTemplateInterface::CORE_CATEGORY]));
        /** @var MailTemplateCollectionInterface $templates */
        $templates = $catalog->listTemplates('classic');
        $this->assertEquals(4, $templates->count());
    }

    public function testListTemplatesWithoutModulesFolder()
    {
        $catalog = new MailTemplateFolderCatalog($this->tempDir);
        $this->fs->remove(implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'classic', MailTemplateInterface::MODULES_CATEGORY]));
        /** @var MailTemplateCollectionInterface $templates */
        $templates = $catalog->listTemplates('classic');
        $this->assertEquals(4, $templates->count());
    }

    /**
     * @param MailTemplateCollectionInterface $collection
     * @param string $category
     *
     * @return MailTemplateInterface[]
     */
    private function filterTemplatesByCategory(MailTemplateCollectionInterface $collection, $category)
    {
        $templates = [];
        /** @var MailTemplateInterface $template */
        foreach ($collection as $template) {
            if ($category == $template->getCategory()) {
                $templates[] = $template;
            }
        }

        return $templates;
    }

    /**
     * @param MailTemplateCollectionInterface $collection
     * @param string $type
     *
     * @return MailTemplateInterface[]
     */
    private function filterTemplatesByType(MailTemplateCollectionInterface $collection, $type)
    {
        $templates = [];
        /** @var MailTemplateInterface $template */
        foreach ($collection as $template) {
            if ($type == $template->getType()) {
                $templates[] = $template;
            }
        }

        return $templates;
    }

    private function createThemesFiles()
    {
        $this->expectedThemes = [
            'classic',
            'modern',
        ];
        $this->coreTemplates = [
            'account.html.twig',
            'password.html.twig',
            'account.txt.twig',
            'password.txt.twig',
            'order_conf.twig',
            'bank_wire.twig',
        ];
        $this->moduleTemplates = [
            'followup' => [
                'followup_1.html.twig',
                'followup_1.txt.twig',
                'followup_2.twig',
            ],
            'ps_emailalerts' => [
                'return_slip.twig',
                'productoutofstock.twig',
            ],
            'empty_module' => [
            ]
        ];

        foreach ($this->expectedThemes as $theme) {
            //Insert core files
            $themeFolder = $this->tempDir . DIRECTORY_SEPARATOR . $theme;
            $coreFolder = implode(DIRECTORY_SEPARATOR, [$themeFolder, MailTemplateInterface::CORE_CATEGORY]);
            $this->fs->mkdir($coreFolder);
            foreach ($this->coreTemplates as $template) {
                $this->fs->touch(implode(DIRECTORY_SEPARATOR, [$coreFolder, $template]));
            }

            //Insert modules files
            $modulesFolder = $themeFolder . DIRECTORY_SEPARATOR . MailTemplateInterface::MODULES_CATEGORY;
            foreach ($this->moduleTemplates as $moduleName => $moduleTemplates) {
                $moduleFolder = $modulesFolder . DIRECTORY_SEPARATOR . $moduleName;
                $this->fs->mkdir($moduleFolder);
                foreach ($moduleTemplates as $template) {
                    $this->fs->touch(implode(DIRECTORY_SEPARATOR, [$moduleFolder, $template]));
                }
            }

            //Insert components files used in templates
            $componentsFolder = $themeFolder . DIRECTORY_SEPARATOR . 'components';
            $this->fs->mkdir($componentsFolder);
            $this->fs->touch($componentsFolder . DIRECTORY_SEPARATOR . 'title.twig');
            $this->fs->touch($componentsFolder . DIRECTORY_SEPARATOR . 'image.twig');
        }
        $this->fs->mkdir($this->tempDir . DIRECTORY_SEPARATOR . 'empty_dir');
        $this->fs->touch($this->tempDir . DIRECTORY_SEPARATOR . 'useless_file');
    }
}
