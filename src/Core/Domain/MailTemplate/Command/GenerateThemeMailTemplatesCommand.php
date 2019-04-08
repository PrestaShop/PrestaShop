<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command;

/**
 * Class GenerateThemeMailsCommand generates email theme's templates for a specific
 * language. If folders are not overridden in the command then MailTemplateGenerator
 * will use the default output folders (in mails folder).
 */
class GenerateThemeMailTemplatesCommand
{
    /** @var string */
    private $themeName;

    /** @var string */
    private $language;

    /** @var bool */
    private $overwriteTemplates;

    /** @var string */
    private $coreMailsFolder = '';

    /** @var string */
    private $modulesMailFolder = '';

    /**
     * @param string $themeName
     * @param string $language
     * @param bool $overwriteTemplates
     * @param string $coreMailsFolder Output folder for core emails (if left empty the default mails folder will be used)
     * @param string $modulesMailFolder Output folder for modules emails (if left empty the module mails folder will be used)
     */
    public function __construct(
        $themeName,
        $language,
        $overwriteTemplates = false,
        $coreMailsFolder = '',
        $modulesMailFolder = ''
    ) {
        $this->themeName = $themeName;
        $this->language = $language;
        $this->overwriteTemplates = $overwriteTemplates;
        $this->coreMailsFolder = $coreMailsFolder;
        $this->modulesMailFolder = $modulesMailFolder;
    }

    /**
     * @return string
     */
    public function getThemeName()
    {
        return $this->themeName;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return bool
     */
    public function overwriteTemplates()
    {
        return $this->overwriteTemplates;
    }

    /**
     * @return string
     */
    public function getCoreMailsFolder()
    {
        return $this->coreMailsFolder;
    }

    /**
     * @return string
     */
    public function getModulesMailFolder()
    {
        return $this->modulesMailFolder;
    }
}
