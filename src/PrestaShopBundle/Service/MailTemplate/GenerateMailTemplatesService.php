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

namespace PrestaShopBundle\Service\MailTemplate;

use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\MailTemplateGenerator;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeInterface;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;

/**
 * This a useful and easy to use service to generate mail templates. It only has
 * string parameters and therefore can be easily called via a command, controller
 * or any other service without much knowledge of the MailTemplate architecture.
 *
 * It is also defined as a Symfony service with default parameters so that mails
 * are exported in the appropriate folders.
 */
class GenerateMailTemplatesService
{
    /** @var LanguageRepositoryInterface */
    private $languageRepository;

    /** @var ThemeCatalogInterface */
    private $themeCatalog;

    /** @var MailTemplateGenerator */
    private $generator;

    /** @var string */
    private $coreMailsFolder;

    /** @var string */
    private $modulesMailFolder;

    /**
     * @param LanguageRepositoryInterface $languageRepository
     * @param ThemeCatalogInterface $themeCatalog
     * @param MailTemplateGenerator $generator
     * @param string $coreMailsFolder
     * @param string $modulesMailFolder
     */
    public function __construct(
        LanguageRepositoryInterface $languageRepository,
        ThemeCatalogInterface $themeCatalog,
        MailTemplateGenerator $generator,
        $coreMailsFolder,
        $modulesMailFolder
    ) {
        $this->languageRepository = $languageRepository;
        $this->themeCatalog = $themeCatalog;
        $this->generator = $generator;
        $this->coreMailsFolder = $coreMailsFolder;
        $this->modulesMailFolder = $modulesMailFolder;
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

    /**
     * @return string
     */
    public function getCoreMailsFolder()
    {
        return $this->coreMailsFolder;
    }

    /**
     * @param string $coreMailsFolder
     * @return $this
     */
    public function setCoreMailsFolder($coreMailsFolder)
    {
        $this->coreMailsFolder = $coreMailsFolder;

        return $this;
    }

    /**
     * @return string
     */
    public function getModulesMailFolder()
    {
        return $this->modulesMailFolder;
    }

    /**
     * @param string $modulesMailFolder
     * @return $this
     */
    public function setModulesMailFolder($modulesMailFolder)
    {
        $this->modulesMailFolder = $modulesMailFolder;

        return $this;
    }
}
