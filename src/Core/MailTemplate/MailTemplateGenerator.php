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

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class MailTemplateGenerator iterates through the layouts in the provided theme,
 * it uses the Renderer to display them (with the requested LanguageInterface) and
 * then export them as template files in the specified output folder.
 */
class MailTemplateGenerator
{
    use LoggerAwareTrait;

    /** @var MailTemplateRendererInterface */
    private $renderer;

    /** @var Filesystem */
    private $fileSystem;

    /**
     * @param MailTemplateRendererInterface $renderer
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        MailTemplateRendererInterface $renderer,
        LoggerInterface $logger = null
    ) {
        $this->renderer = $renderer;
        $this->logger = null !== $logger ? $logger : new NullLogger();
        $this->fileSystem = new Filesystem();
    }

    /**
     * @param ThemeInterface $theme
     * @param LanguageInterface $language
     * @param string $coreOutputFolder
     * @param string $modulesOutputFolder
     * @param bool $overwriteTemplates [default=false]
     *
     * @throws FileNotFoundException
     */
    public function generateTemplates(
        ThemeInterface $theme,
        LanguageInterface $language,
        $coreOutputFolder,
        $modulesOutputFolder,
        $overwriteTemplates = false
    ) {
        if (!is_dir($coreOutputFolder)) {
            throw new FileNotFoundException(sprintf('Invalid core output folder "%s"', $coreOutputFolder));
        }

        if (!is_dir($modulesOutputFolder)) {
            throw new FileNotFoundException(sprintf('Invalid modules output folder "%s"', $modulesOutputFolder));
        }

        $this->logger->info(sprintf('Exporting mail with theme %s for language %s', $theme->getName(), $language->getName()));
        $this->logger->info(sprintf('Core output folder: %s', $coreOutputFolder));
        $this->logger->info(sprintf('Modules output folder: %s', $modulesOutputFolder));

        /** @var LayoutCollectionInterface $layouts */
        $layouts = $theme->getLayouts();
        /** @var LayoutInterface $layout */
        foreach ($layouts as $layout) {
            if (!empty($layout->getModuleName())) {
                $outputFolder = implode(DIRECTORY_SEPARATOR, [$modulesOutputFolder, $layout->getModuleName(), 'mails', $language->getIsoCode()]);
            } else {
                $outputFolder = implode(DIRECTORY_SEPARATOR, [$coreOutputFolder, $language->getIsoCode()]);
            }

            //Generate HTML template
            $htmlTemplatePath = $this->generateTemplatePath($layout, MailTemplateInterface::HTML_TYPE, $outputFolder);
            if (!$this->fileSystem->exists($htmlTemplatePath) || $overwriteTemplates) {
                $generatedTemplate = $this->renderer->renderHtml($layout, $language);
                $this->fileSystem->dumpFile($htmlTemplatePath, $generatedTemplate);
                $this->logger->info(sprintf('Generate html template %s at %s', $layout->getName(), $htmlTemplatePath));
            }

            //Generate TXT template
            $txtTemplatePath = $this->generateTemplatePath($layout, MailTemplateInterface::TXT_TYPE, $outputFolder);
            if (!$this->fileSystem->exists($txtTemplatePath) || $overwriteTemplates) {
                $generatedTemplate = $this->renderer->renderTxt($layout, $language);
                $this->fileSystem->dumpFile($txtTemplatePath, $generatedTemplate);
                $this->logger->info(sprintf('Generate txt template %s at %s', $layout->getName(), $txtTemplatePath));
            }
        }
    }

    /**
     * @param LayoutInterface $layout
     * @param string $templateType
     * @param string $outputFolder
     *
     * @return string
     */
    private function generateTemplatePath(LayoutInterface $layout, $templateType, $outputFolder)
    {
        return implode(DIRECTORY_SEPARATOR, [$outputFolder, $layout->getName()]) . '.' . $templateType;
    }
}
