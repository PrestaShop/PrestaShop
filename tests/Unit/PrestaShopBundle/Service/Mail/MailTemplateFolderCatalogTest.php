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

    public function testListTemplates()
    {
        $catalog = new MailTemplateFolderCatalog($this->tempDir);
        $templateCollection = $catalog->listTemplates('classic');
        $this->assertNotNull($templateCollection);
        $this->assertInstanceOf(MailTemplateCollectionInterface::class, $templateCollection);
        $this->assertEquals(10, $templateCollection->count());

        //Check core templates
        $coreTemplates = $this->filterTemplatesByType($templateCollection, MailTemplateInterface::CORE_TEMPLATES);
        $this->assertCount(5, $coreTemplates);

        /** @var MailTemplateInterface $template */
        $template = $coreTemplates[0];
        $this->assertInstanceOf(MailTemplateInterface::class, $template);
        $expectedPath = implode(DIRECTORY_SEPARATOR, [
            realpath($this->tempDir),
            'classic',
            MailTemplateInterface::CORE_TEMPLATES,
            'account.html.twig',
        ]);
        $this->assertEquals($expectedPath, $template->getPath());
        $this->assertEquals('classic', $template->getTheme());
        $this->assertEquals('account.html', $template->getName());
        $this->assertEquals(MailTemplateInterface::CORE_TEMPLATES, $template->getType());
        $this->assertNull($template->getModule());

        //Check module templates
        $modulesTemplates = $this->filterTemplatesByType($templateCollection, MailTemplateInterface::MODULES_TEMPLATES);
        $this->assertCount(5, $modulesTemplates);

        $moduleTemplatesCount = [];
        /** @var MailTemplateInterface $moduleTemplate */
        foreach ($modulesTemplates as $moduleTemplate) {
            $this->assertEquals('classic', $template->getTheme());
            $this->assertEquals(MailTemplateInterface::MODULES_TEMPLATES, $moduleTemplate->getType());
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
            'account.txt.twig',
            'password.twig',
            'order_conf.twig',
            'bank_wire.twig',
        ];
        $this->moduleTemplates = [
            'followup' => [
                'followup_1.twig',
                'followup_2.twig',
                'followup_3.twig',
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
            $coreFolder = $themeFolder . DIRECTORY_SEPARATOR . MailTemplateInterface::CORE_TEMPLATES;
            $this->fs->mkdir($coreFolder);
            foreach ($this->coreTemplates as $template) {
                $this->fs->touch($coreFolder . DIRECTORY_SEPARATOR . $template);
            }

            //Insert modules files
            $modulesFolder = $themeFolder . DIRECTORY_SEPARATOR . MailTemplateInterface::MODULES_TEMPLATES;
            foreach ($this->moduleTemplates as $moduleName => $moduleTemplates) {
                $moduleFolder = $modulesFolder . DIRECTORY_SEPARATOR . $moduleName;
                $this->fs->mkdir($moduleFolder);
                foreach ($moduleTemplates as $moduleTemplate) {
                    $this->fs->touch($moduleFolder . DIRECTORY_SEPARATOR . $moduleTemplate);
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
