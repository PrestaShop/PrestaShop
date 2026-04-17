<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     * @param string $language locale, for example: 'en'
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
