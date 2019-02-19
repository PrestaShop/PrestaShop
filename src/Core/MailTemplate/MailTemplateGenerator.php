<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutCollectionInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\LayoutInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class MailTemplateGenerator iterates through the layouts in the provided catalog,
 * it uses the Renderer to display them (with the requested LanguageInterface) and
 * then export them as template files in the specified output folder.
 */
class MailTemplateGenerator
{
    use LoggerAwareTrait;

    /** @var LayoutCatalogInterface */
    private $catalog;

    /** @var MailTemplateRendererInterface */
    private $renderer;

    /** @var Filesystem */
    private $fileSystem;

    /**
     * @param LayoutCatalogInterface $catalog
     * @param MailTemplateRendererInterface $renderer
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        LayoutCatalogInterface $catalog,
        MailTemplateRendererInterface $renderer,
        LoggerInterface $logger = null
    ) {
        $this->catalog = $catalog;
        $this->renderer = $renderer;
        $this->logger = null !== $logger ? $logger : new NullLogger();
        $this->fileSystem = new Filesystem();
    }

    /**
     * @param string $theme
     * @param LanguageInterface $language
     * @param string $coreOutputFolder
     * @param string $modulesOutputFolder
     *
     * @throws FileNotFoundException
     * @throws InvalidArgumentException
     */
    public function generateThemeTemplates($theme, LanguageInterface $language, $coreOutputFolder, $modulesOutputFolder)
    {
        $this->checkMailTheme($theme);

        if (!is_dir($coreOutputFolder)) {
            throw new FileNotFoundException(sprintf(
                'Invalid core output folder "%s"',
                $coreOutputFolder
            ));
        }

        if (!is_dir($modulesOutputFolder)) {
            throw new FileNotFoundException(sprintf(
                'Invalid modules output folder "%s"',
                $modulesOutputFolder
            ));
        }

        /** @var LayoutCollectionInterface $layouts */
        $layouts = $this->catalog->listLayouts($theme);
        /** @var LayoutInterface $layout */
        foreach ($layouts as $layout) {
            if (!empty($layout->getModuleName())) {
                $outputFolder = implode(DIRECTORY_SEPARATOR, [$modulesOutputFolder, $layout->getModuleName(), 'mails']);
            } else {
                $outputFolder = $coreOutputFolder;
            }

            //Generate HTML template
            $generatedTemplate = $this->renderer->renderHtml($layout, $language);
            $htmlTemplatePath = $this->generateTemplatePath($layout, MailTemplateInterface::HTML_TYPE, $outputFolder);
            $this->fileSystem->dumpFile($htmlTemplatePath, $generatedTemplate);

            //Generate TXT template
            $generatedTemplate = $this->renderer->renderTxt($layout, $language);
            $txtTemplatePath = $this->generateTemplatePath($layout, MailTemplateInterface::TXT_TYPE, $outputFolder);
            $this->fileSystem->dumpFile($txtTemplatePath, $generatedTemplate);
            $this->logger->info(sprintf('Generate template %s at html: %s, txt: %s', $layout->getName(), $htmlTemplatePath, $txtTemplatePath));
        }
    }

    /**
     * @param string $theme
     *
     * @throws InvalidArgumentException
     */
    private function checkMailTheme($theme)
    {
        /** @var MailThemeCollectionInterface $availableThemes */
        $availableThemes = $this->catalog->listThemes();
        $themeNames = [];
        /** @var MailThemeInterface $availableTheme */
        foreach ($availableThemes as $availableTheme) {
            $themeNames[] = $availableTheme->getName();
        }
        if (!in_array($theme, $themeNames)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid theme used "%s", only available themes are: %s',
                $theme,
                implode(', ', $themeNames)
            ));
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
