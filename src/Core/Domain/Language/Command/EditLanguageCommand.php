<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\TagIETF;

/**
 * Edits given language with provided data
 */
class EditLanguageCommand
{
    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var IsoCode|null
     */
    private $isoCode;

    /**
     * @var TagIETF|null
     */
    private $tagIETF;

    /**
     * @var string|null
     */
    private $shortDateFormat;

    /**
     * @var string|null
     */
    private $fullDateFormat;

    /**
     * @var string|null
     */
    private $flagImagePath;

    /**
     * @var string|null
     */
    private $noPictureImagePath;

    /**
     * @var bool|null
     */
    private $isRtl;

    /**
     * @var bool|null
     */
    private $isActive;

    /**
     * @var int[]|null
     */
    private $shopAssociation;

    /**
     * @param int $languageId
     */
    public function __construct($languageId)
    {
        $this->languageId = new LanguageId($languageId);
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }

    /**
     * @param LanguageId $languageId
     *
     * @return self
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return IsoCode|null
     */
    public function getIsoCode()
    {
        return $this->isoCode;
    }

    /**
     * @param string $isoCode
     *
     * @return self
     */
    public function setIsoCode($isoCode)
    {
        $this->isoCode = new IsoCode($isoCode);

        return $this;
    }

    /**
     * @return TagIETF|null
     */
    public function getTagIETF()
    {
        return $this->tagIETF;
    }

    /**
     * @param string $tagIETF
     *
     * @return self
     */
    public function setTagIETF($tagIETF)
    {
        $this->tagIETF = new TagIETF($tagIETF);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShortDateFormat()
    {
        return $this->shortDateFormat;
    }

    /**
     * @param string $shortDateFormat
     *
     * @return self
     */
    public function setShortDateFormat($shortDateFormat)
    {
        $this->shortDateFormat = $shortDateFormat;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFullDateFormat()
    {
        return $this->fullDateFormat;
    }

    /**
     * @param string $fullDateFormat
     *
     * @return self
     */
    public function setFullDateFormat($fullDateFormat)
    {
        $this->fullDateFormat = $fullDateFormat;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFlagImagePath()
    {
        return $this->flagImagePath;
    }

    /**
     * @param string $flagImagePath
     *
     * @return self
     */
    public function setFlagImagePath($flagImagePath)
    {
        $this->flagImagePath = $flagImagePath;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNoPictureImagePath()
    {
        return $this->noPictureImagePath;
    }

    /**
     * @param string $noPictureImagePath
     *
     * @return self
     */
    public function setNoPictureImagePath($noPictureImagePath)
    {
        $this->noPictureImagePath = $noPictureImagePath;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isRtl()
    {
        return $this->isRtl;
    }

    /**
     * @param bool $isRtl
     *
     * @return self
     */
    public function setIsRtl($isRtl)
    {
        $this->isRtl = $isRtl;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     *
     * @return self
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return int[]|null
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }

    /**
     * @param int[] $shopAssociation
     *
     * @return self
     */
    public function setShopAssociation(array $shopAssociation)
    {
        $this->shopAssociation = $shopAssociation;

        return $this;
    }
}
