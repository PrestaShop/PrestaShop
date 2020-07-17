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

namespace PrestaShop\PrestaShop\Core\MailTemplate\Layout;

/**
 * Class MailLayout is the default implementation of MailLayoutInterface,
 * it is a simple immutable data container with no logic. It contains the
 * basic info about a mail layout which is used to generate a MailTemplate.
 */
class Layout implements LayoutInterface
{
    /** @var string */
    private $name;

    /** @var string */
    private $htmlPath;

    /** @var string */
    private $txtPath;

    /** @var string */
    private $moduleName;

    /**
     * @param string $name Name of the layout to describe its purpose
     * @param string $htmlPath Absolute path of the html layout file
     * @param string $txtPath Absolute path of the txt layout file
     * @param string $moduleName Which module this layout is associated to (if any)
     */
    public function __construct(
        $name,
        $htmlPath,
        $txtPath,
        $moduleName = ''
    ) {
        $this->name = $name;
        $this->htmlPath = $htmlPath;
        $this->txtPath = $txtPath;
        $this->moduleName = $moduleName;
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
