<?php
/**
 * 2007-2019 PrestaShop.
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
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Composer;

use PrestaShop\PrestaShop\Core\Composer\Exception\ComposerException;

final class ModuleInformation
{
    private $owner;

    private $name;

    private $version;

    public function __construct($owner, $name, $version)
    {
        $this->owner = $owner;
        $this->name = $name;
        $this->version = $version;
    }

    public static function createFromString($expression, $version)
    {
        list($owner, $name) = explode('/', $expression);

        if (empty($owner) || empty($name)) {
            ComposerException::invalidModuleExpression($expression);
        }

        return new self($owner, $name, $version);
    }

    public function getOwner()
    {
        return $this->owner;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getVersion()
    {
        return $this->version;
    }
}
