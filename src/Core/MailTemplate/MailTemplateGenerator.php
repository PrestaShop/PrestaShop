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

use PrestaShop\PrestaShop\Core\Exception\InvalidException;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;
use Language;

/**
 * Class MailTemplateGenerator iterates through the template in the provided catalog,
 * it uses the renderer to output them (with the requested Language) and then export
 * them as files in the specified output folder.
 */
class MailTemplateGenerator
{
    use LoggerAwareTrait;

    /** @var MailTemplateCatalogInterface */
    private $catalog;

    /** @var MailTemplateRendererInterface */
    private $renderer;

    /** @var Filesystem */
    private $fileSystem;

    public function __construct(
        MailTemplateCatalogInterface $catalog,
        MailTemplateRendererInterface $renderer
    ) {
        $this->catalog = $catalog;
        $this->renderer = $renderer;
        $this->logger = new NullLogger();
        $this->fileSystem = new Filesystem();
    }

    /**
     * @param string $theme
     * @param Language $language
     * @param string $outputFolder
     *
     * @throws InvalidException
     */
    public function generateThemeTemplates($theme, Language $language, $outputFolder)
    {
        $availableThemes = $this->catalog->listThemes();
        if (!in_array($theme, $availableThemes)) {
            throw new InvalidException(sprintf(
                'Invalid theme used "%s", only available themes are: %s',
                $theme,
                implode(', ', $availableThemes)
            ));
        }

        if (!$this->fileSystem->exists($outputFolder) || !is_dir($outputFolder)) {
            throw new InvalidException(sprintf(
                'Invalid output folder "%s"',
                $outputFolder
            ));
        }

        /** @var MailTemplateCollectionInterface $templates */
        $templates = $this->catalog->listTemplates($theme);
        /** @var MailTemplateInterface $template */
        foreach ($templates as $template) {
            $generatedTemplate = $this->renderer->render($template, $language);
            $templatePath = $this->generateTemplatePath($template, $outputFolder);
            $this->fileSystem->dumpFile($templatePath, $generatedTemplate);
            $this->logger->info(sprintf('Generate template %s at %s', $template->getName(), $templatePath));
        }
    }

    /**
     * @param MailTemplateInterface $template
     * @param string $outputFolder
     *
     * @return string
     */
    private function generateTemplatePath(MailTemplateInterface $template, $outputFolder)
    {
        return implode(DIRECTORY_SEPARATOR, [$outputFolder, $template->getName()]) . '.' . $template->getType();
    }
}
