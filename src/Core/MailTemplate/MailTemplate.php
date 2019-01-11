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

/**
 * Class MailTemplate is the default implementation of MailTemplateInterface,
 * it is a simple data container with no logic.
 */
class MailTemplate implements MailTemplateInterface
{
    /** @var string */
    private $theme;

    /** @var string */
    private $category;

    /** @var string */
    private $name;

    /** @var string */
    private $htmlPath;

    /** @var string */
    private $txtPath;

    /** @var string */
    private $moduleName;

    /**
     * MailTemplate constructor.
     *
     * @param string $theme Which theme this template is associated to
     * @param string $category Whether the template is used by the core or modules
     * @param string $name Name of the template to describe its purpose
     * @param string $htmlPath Absolute path of the html template file
     * @param string $txtPath Absolute path of the txt template file
     * @param string $moduleName Which module this template is associated to (if any)
     */
    public function __construct(
        $theme,
        $category,
        $name,
        $htmlPath = '',
        $txtPath = '',
        $moduleName = ''
    ) {
        $this->theme = $theme;
        $this->category = $category;
        $this->name = $name;
        $this->htmlPath = $htmlPath;
        $this->txtPath = $txtPath;
        $this->moduleName = $moduleName;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * @return string
     */
    public function getHtmlPath()
    {
        return $this->htmlPath;
    }

    /**
     * @return string
     */
    public function getTxtPath()
    {
        return $this->txtPath;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }
}
