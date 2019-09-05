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

namespace PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command;

/**
 * Creates manufacturer with provided data
 */
class AddManufacturerCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $localizedShortDescriptions;

    /**
     * @var string[]
     */
    private $localizedDescriptions;

    /**
     * @var string[]
     */
    private $localizedMetaTitles;

    /**
     * @var string[]
     */
    private $localizedMetaDescriptions;

    /**
     * @var string[]
     */
    private $localizedMetaKeywords;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var array
     */
    private $shopAssociation;

    /**
     * @param string $name
     * @param bool $enabled
     * @param string[] $localizedShortDescriptions
     * @param string[] $localizedDescriptions
     * @param string[] $localizedMetaTitles
     * @param string[] $localizedMetaDescriptions
     * @param string[] $localizedMetaKeywords
     * @param array $shopAssociation
     */
    public function __construct(
        $name,
        $enabled,
        array $localizedShortDescriptions,
        array $localizedDescriptions,
        array $localizedMetaTitles,
        array $localizedMetaDescriptions,
        array $localizedMetaKeywords,
        array $shopAssociation
    ) {
        $this->name = $name;
        $this->enabled = $enabled;
        $this->localizedShortDescriptions = $localizedShortDescriptions;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->localizedMetaTitles = $localizedMetaTitles;
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;
        $this->localizedMetaKeywords = $localizedMetaKeywords;
        $this->shopAssociation = $shopAssociation;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string[]
     */
    public function getLocalizedShortDescriptions()
    {
        return $this->localizedShortDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions()
    {
        return $this->localizedDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitles()
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescriptions()
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaKeywords()
    {
        return $this->localizedMetaKeywords;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return array
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }
}
