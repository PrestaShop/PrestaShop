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

namespace PrestaShop\PrestaShop\Core\Domain\MailTemplate\Command;

class GenerateThemeMailsCommand
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
     */
    public function __construct(
        $themeName,
        $language,
        $overwriteTemplates = false
    ) {
        $this
            ->setThemeName($themeName)
            ->setLanguage($language)
            ->setOverwriteTemplates($overwriteTemplates)
        ;
    }

    /**
     * @return string
     */
    public function getThemeName()
    {
        return $this->themeName;
    }

    /**
     * @param string $themeName
     *
     * @return GenerateThemeMailsCommand
     */
    public function setThemeName($themeName)
    {
        $this->themeName = $themeName;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     *
     * @return GenerateThemeMailsCommand
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return bool
     */
    public function overwriteTemplates()
    {
        return $this->overwriteTemplates;
    }

    /**
     * @param bool $overwriteTemplates
     *
     * @return GenerateThemeMailsCommand
     */
    public function setOverwriteTemplates($overwriteTemplates)
    {
        $this->overwriteTemplates = $overwriteTemplates;

        return $this;
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
     *
     * @return GenerateThemeMailsCommand
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
     *
     * @return GenerateThemeMailsCommand
     */
    public function setModulesMailFolder($modulesMailFolder)
    {
        $this->modulesMailFolder = $modulesMailFolder;

        return $this;
    }
}
