<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command\GenerateThemeMailTemplatesCommand;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateGenerator;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;

/**
 * Class GenerateThemeMailTemplatesHandler generates email templates with parameters provided
 * by GenerateThemeMailTemplatesCommand. If no output folders are defined by the command its output
 * folders are the default ones.
 */
#[AsCommandHandler]
class GenerateThemeMailTemplatesHandler implements GenerateThemeMailTemplatesHandlerInterface
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
    public function handle(GenerateThemeMailTemplatesCommand $command)
    {
        $language = $this->languageRepository->getOneByLocaleOrIsoCode($command->getLanguage());
        if (null === $language) {
            throw new InvalidArgumentException(sprintf('Could not find Language for locale: %s', $command->getLanguage()));
        }

        /** @var ThemeInterface $theme */
        $theme = $this->themeCatalog->getByName($command->getThemeName());

        $coreMailsFolder = $command->getCoreMailsFolder() ?: $this->defaultCoreMailsFolder;
        $modulesMailFolder = $command->getModulesMailFolder() ?: $this->defaultModulesMailFolder;

        $this->generator->generateTemplates($theme, $language, $coreMailsFolder, $modulesMailFolder, $command->overwriteTemplates());
    }
}
