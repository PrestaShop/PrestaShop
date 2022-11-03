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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\TagIETF;

/**
 * Transfers editable language's data
 */
class EditableLanguage
{
    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var IsoCode
     */
    private $isoCode;

    /**
     * @var TagIETF
     */
    private $tagIETF;

    /**
     * @var string
     */
    private $shortDateFormat;

    /**
     * @var string
     */
    private $fullDateFormat;

    /**
     * @var bool
     */
    private $isRtl;

    /**
     * @var bool
     */
    private $isActive;

    /**
     * @var array
     */
    private $shopAssociation;

    /**
     * @param LanguageId $languageId
     * @param string $name
     * @param IsoCode $isoCode
     * @param TagIETF $tagIETF
     * @param string $shortDateFormat
     * @param string $fullDateFormat
     * @param bool $isRtl
     * @param bool $isActive
     * @param array $shopAssociation
     */
    public function __construct(
        LanguageId $languageId,
        $name,
        IsoCode $isoCode,
        TagIETF $tagIETF,
        $shortDateFormat,
        $fullDateFormat,
        $isRtl,
        $isActive,
        array $shopAssociation
    ) {
        $this->languageId = $languageId;
        $this->name = $name;
        $this->isoCode = $isoCode;
        $this->tagIETF = $tagIETF;
        $this->shortDateFormat = $shortDateFormat;
        $this->fullDateFormat = $fullDateFormat;
        $this->isRtl = $isRtl;
        $this->isActive = $isActive;
        $this->shopAssociation = $shopAssociation;
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId()
    {
        return $this->languageId;
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
     * @return TagIETF
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
    public function isRtl()
    {
        return $this->isRtl;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @return array
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }
}
