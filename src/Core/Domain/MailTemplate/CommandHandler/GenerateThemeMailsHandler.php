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

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailsCommand;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateGenerator;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;

/**
 * Class GenerateThemeMailsHandler handles a GenerateThemeMailsCommand, it is able
 * to transform raw string information contained in the command object from the command
 * into real objects necessary for the MailTemplateGenerator (get LanguageInterface, ThemeInterface).
 * It also manages the default output folder if they are not overridden by the command.
 */
class GenerateThemeMailsHandler implements GenerateThemeMailsHandlerInterface
{
    /** @var LanguageRepositoryInterface */
    private $languageRepository;

    /** @var ThemeCatalogInterface */
    private $themeCatalog;

    /** @var MailTemplateGenerator */
    private $generator;

    /** @var string */
    private $defaultCoreMailsFolder;

    /** @var string */
    private $defaultModulesMailFolder;

    /**
     * @param LanguageRepositoryInterface $languageRepository
     * @param ThemeCatalogInterface $themeCatalog
     * @param MailTemplateGenerator $generator
     * @param string $defaultCoreMailsFolder
     * @param string $defaultModulesMailFolder
     */
    public function __construct(
        LanguageRepositoryInterface $languageRepository,
        ThemeCatalogInterface $themeCatalog,
        MailTemplateGenerator $generator,
        $defaultCoreMailsFolder,
        $defaultModulesMailFolder
    ) {
        $this->languageRepository = $languageRepository;
        $this->themeCatalog = $themeCatalog;
        $this->generator = $generator;
        $this->defaultCoreMailsFolder = $defaultCoreMailsFolder;
        $this->defaultModulesMailFolder = $defaultModulesMailFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(GenerateThemeMailsCommand $command)
    {
        /** @var LanguageInterface $language */
        $language = $this->languageRepository->getByLocaleOrIsoCode($command->getLanguage());
        if (null === $language) {
            throw new InvalidArgumentException(sprintf('Could not find Language for locale: %s', $command->getLanguage()));
        }

        /** @var ThemeInterface $theme */
        $theme = $this->themeCatalog->getByName($command->getThemeName());

        $coreMailsFolder = !empty($command->getCoreMailsFolder()) ? $command->getCoreMailsFolder() : $this->defaultCoreMailsFolder;
        $modulesMailFolder = !empty($command->getModulesMailFolder()) ? $command->getModulesMailFolder() : $this->defaultModulesMailFolder;

        $this->generator->generateTemplates($theme, $language, $coreMailsFolder, $modulesMailFolder, $command->overwriteTemplates());
    }

    /**
     * @param string $themeName
     * @param string $locale
     * @param bool $overwriteTemplates
     *
     * @throws InvalidArgumentException
     * @throws FileNotFoundException
     */
    public function generateMailTemplates($themeName, $locale, $overwriteTemplates = false)
    {
        /** @var LanguageInterface $language */
        $language = $this->languageRepository->getByLocaleOrIsoCode($locale);
        if (null === $language) {
            throw new InvalidArgumentException(sprintf('Could not find Language for locale: %s', $locale));
        }

        /** @var ThemeInterface $theme */
        $theme = $this->themeCatalog->getByName($themeName);

        $this->generator->generateTemplates($theme, $language, $this->coreMailsFolder, $this->modulesMailFolder, $overwriteTemplates);
    }
}
