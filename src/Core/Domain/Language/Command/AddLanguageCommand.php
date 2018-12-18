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

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;

/**
 * Adds new language with given data
 */
class AddLanguageCommand
{
    /**
     * @var string Language's name
     */
    private $name;

    /**
     * @var IsoCode Two-letter (639-1) language ISO code, e.g. FR, EN
     */
    private $isoCode;

    /**
     * @var string IETF language tag, e.g. en-US
     */
    private $tagIETF;

    /**
     * @var string Short date format. e.g. Y-m-d
     */
    private $shortDateFormat;

    /**
     * @var string Full date format, e.g. Y-m-d H:i:s
     */
    private $fullDateFormat;
    /**
     * @var bool Is language read from right to left
     */
    private $isRtlLanguage;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var int[] ID of shops which are associated with language
     */
    private $shopAssociation;

    /**
     * @param string $name
     * @param string $isoCode
     * @param string $tagIETF
     * @param string $shortDateFormat
     * @param string $fullDateFormat
     * @param bool $isRtlLanguage
     * @param bool $isActive
     * @param int[] $shopAssociation
     */
    public function __construct(
        $name,
        $isoCode,
        $tagIETF,
        $shortDateFormat,
        $fullDateFormat,
        $isRtlLanguage = false,
        $isActive = true,
        array $shopAssociation = []
    ) {
        $this->name = $name;
        $this->isoCode = new IsoCode($isoCode);
        $this->tagIETF = $tagIETF;
        $this->shortDateFormat = $shortDateFormat;
        $this->fullDateFormat = $fullDateFormat;
        $this->isRtlLanguage = $isRtlLanguage;
        $this->isActive = $isActive;
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
     * @return IsoCode
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @return string
     */
    public function getTagIETF()
    {
        return $this->tagIETF;
    }

    /**
     * @return string
     */
    public function getShortDateFormat()
    {
        return $this->shortDateFormat;
    }

    /**
     * @return string
     */
    public function getFullDateFormat()
    {
        return $this->fullDateFormat;
    }

    /**
     * @return bool
     */
    public function isRtlLanguage()
    {
        return $this->isRtlLanguage;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return int[]
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }
}
