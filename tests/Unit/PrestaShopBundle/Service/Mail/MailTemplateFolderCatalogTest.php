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
use PrestaShopBundle\Service\Mail\MailTemplateCollectionInterface;
use PrestaShopBundle\Service\Mail\MailTemplateFolderCatalog;
use PrestaShopBundle\Service\Mail\MailTemplateInterface;
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
        $this->assertEquals(11, $templateCollection->count());

        //Check core templates
        $coreTemplates = $this->filterTemplatesByCategory($templateCollection, MailTemplateInterface::CORE_CATEGORY);
        $this->assertCount(6, $coreTemplates);

        /** @var MailTemplateInterface $template */
        $template = $coreTemplates[0];
        $this->assertInstanceOf(MailTemplateInterface::class, $template);
        $expectedPath = implode(DIRECTORY_SEPARATOR, [
            realpath($this->tempDir),
            'classic',
            MailTemplateInterface::CORE_CATEGORY,
            MailTemplateInterface::HTML_TYPE,
            'account.html.twig',
        ]);
        $this->assertEquals($expectedPath, $template->getPath());
        $this->assertEquals('classic', $template->getTheme());
        $this->assertEquals('account.html', $template->getName());
        $this->assertEquals(MailTemplateInterface::CORE_CATEGORY, $template->getCategory());
        $this->assertEquals(MailTemplateInterface::HTML_TYPE, $template->getType());
        $this->assertNull($template->getModule());

        //Check module templates
        $modulesTemplates = $this->filterTemplatesByCategory($templateCollection, MailTemplateInterface::MODULES_CATEGORY);
        $this->assertCount(5, $modulesTemplates);

        $moduleTemplatesCount = [];
        /** @var MailTemplateInterface $moduleTemplate */
        foreach ($modulesTemplates as $moduleTemplate) {
            $this->assertEquals('classic', $template->getTheme());
            $this->assertEquals(MailTemplateInterface::MODULES_CATEGORY, $moduleTemplate->getCategory());
            $this->assertContains($moduleTemplate->getType(), [MailTemplateInterface::HTML_TYPE, MailTemplateInterface::RAW_TYPE]);
            $this->assertNotNull($moduleTemplate->getModule());
            if (!isset($moduleTemplatesCount[$moduleTemplate->getModule()])) {
                $moduleTemplatesCount[$moduleTemplate->getModule()] = [$moduleTemplate->getName()];
            } else {
                $moduleTemplatesCount[$moduleTemplate->getModule()][] = $moduleTemplate->getName();
            }
        }
        $this->assertCount(3, $moduleTemplatesCount['followup']);
        $this->assertEquals(['followup_1', 'followup_2', 'followup_3'], $moduleTemplatesCount['followup']);
        $this->assertCount(2, $moduleTemplatesCount['ps_emailalerts']);
        $this->assertEquals(['productoutofstock', 'return_slip'], $moduleTemplatesCount['ps_emailalerts']);

        //Check templates types
        $htmlTemplates = $this->filterTemplatesByType($templateCollection, MailTemplateInterface::HTML_TYPE);
        $this->assertCount(5, $htmlTemplates);
        $rawTemplates = $this->filterTemplatesByType($templateCollection, MailTemplateInterface::RAW_TYPE);
        $this->assertCount(6, $rawTemplates);
    }

    public function testListTemplatesWithoutCoreFolder()
    {
        $catalog = new MailTemplateFolderCatalog($this->tempDir);
        //No bug occurs if the folder does not exist
        $this->fs->remove(implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'classic', MailTemplateInterface::CORE_CATEGORY]));
        /** @var MailTemplateCollectionInterface $templates */
        $templates = $catalog->listTemplates('classic');
        $this->assertEquals(5, $templates->count());
    }

    public function testListTemplatesWithoutModulesFolder()
    {
        $catalog = new MailTemplateFolderCatalog($this->tempDir);
        $this->fs->remove(implode(DIRECTORY_SEPARATOR, [$this->tempDir, 'classic', MailTemplateInterface::MODULES_CATEGORY]));
        /** @var MailTemplateCollectionInterface $templates */
        $templates = $catalog->listTemplates('classic');
        $this->assertEquals(6, $templates->count());
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
            MailTemplateInterface::HTML_TYPE => [
                'account.html.twig',
                'password.html.twig',
            ],
            MailTemplateInterface::RAW_TYPE => [
                'account.txt.twig',
                'password.txt.twig',
                'order_conf.twig',
                'bank_wire.twig',
            ],
        ];
        $this->moduleTemplates = [
            'followup' => [
                MailTemplateInterface::HTML_TYPE => [
                    'followup_1.twig',
                    'followup_2.twig',
                    'followup_3.twig',
                ],
            ],
            'ps_emailalerts' => [
                MailTemplateInterface::RAW_TYPE => [
                    'return_slip.twig',
                    'productoutofstock.twig',
                ],
            ],
            'empty_module' => [
            ]
        ];

        foreach ($this->expectedThemes as $theme) {
            //Insert core files
            $themeFolder = $this->tempDir . DIRECTORY_SEPARATOR . $theme;
            $coreFolder = implode(DIRECTORY_SEPARATOR, [$themeFolder, MailTemplateInterface::CORE_CATEGORY]);
            foreach ($this->coreTemplates as $templateType => $templates) {
                $typeFolder = implode(DIRECTORY_SEPARATOR, [$coreFolder, $templateType]);
                $this->fs->mkdir($typeFolder);
                foreach ($templates as $template) {
                    $this->fs->touch(implode(DIRECTORY_SEPARATOR, [$typeFolder, $template]));
                }
            }

            //Insert modules files
            $modulesFolder = $themeFolder . DIRECTORY_SEPARATOR . MailTemplateInterface::MODULES_CATEGORY;
            foreach ($this->moduleTemplates as $moduleName => $moduleTemplates) {
                $moduleFolder = $modulesFolder . DIRECTORY_SEPARATOR . $moduleName;
                foreach ($moduleTemplates as $templateType => $templates) {
                    $typeFolder = implode(DIRECTORY_SEPARATOR, [$moduleFolder, $templateType]);
                    $this->fs->mkdir($typeFolder);
                    foreach ($templates as $template) {
                        $this->fs->touch(implode(DIRECTORY_SEPARATOR, [$typeFolder, $template]));
                    }
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
