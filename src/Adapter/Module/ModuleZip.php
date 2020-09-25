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

namespace PrestaShop\PrestaShop\Adapter\Module;

/**
 * Value object used to represent a partial Module object during unzipping
 * of a module archive.
 */
class ModuleZip
{
    /**
     * @var string Module technical name, guessed from the path [module name]/[module name].php
     */
    private $name;

    /**
     * @var string Temporary path to extract the module files before going into the modules folder
     */
    private $sandboxPath;

    /**
     * @var string Complete path to the source file (Zip)
     */
    private $source;

    public function __construct($source)
    {
        $this->name = null;
        $this->sandboxPath = null;
        $this->source = $source;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @return string|null
     */
    public function getSandboxPath()
    {
        return $this->sandboxPath;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param $sandboxPath
     *
     * @return $this
     */
    public function setSandboxPath($sandboxPath)
    {
        $this->sandboxPath = $sandboxPath;

        return $this;
    }
}
