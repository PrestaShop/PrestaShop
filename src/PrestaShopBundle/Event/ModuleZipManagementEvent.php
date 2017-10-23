<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class ModuleZipManagementEvent extends Event
{
    const DOWNLOAD = 'module.download'; // Module download from addons or employee disk

    private $name;
    private $source;

    // ToDo: To be replaced with a specific Module Zip class rather than an array
    public function __construct(array $source)
    {
        $this->name = $source['name'];
        $this->source = $source['source'];
    }

    public function getModuleName()
    {
        return $this->name;
    }

    public function getSource()
    {
        return $this->source;
    }
}
