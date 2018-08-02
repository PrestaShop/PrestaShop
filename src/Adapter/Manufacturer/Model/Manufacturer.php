<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\Model;

use Manufacturer as LegacyManufacturer;

/**
 * Define what is a manufacturer.
 */
final class Manufacturer implements ManufacturerInterface
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $languageId;

    /**
     * Manufacturer constructor: we do it only for validate the integrity of data.
     * @param null $id
     * @param null $languageId
     */
    public function __construct($id = null, $languageId = null)
    {
        LegacyManufacturer::__construct($id, $languageId);
        $this->id = $id;
        $this->languageId = $languageId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLanguageId()
    {
        return $this->languageId;
    }
}
