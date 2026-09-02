<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Language\Command;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\IsoCode;
use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\TagIETF;

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
     * @var TagIETF IETF language tag, e.g. en-US
     */
    private $tagIETF;

    /**
     * @var string
     */
    private $flagImagePath;

    /**
     * @var string Image that is used as fallback image for product, category and brand
     *             when the actual image is not available.
     *             Each language has different "No picture" image.
     */
    private $noPictureImagePath;

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
    private $isRtl;

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
     * @param string $flagImagePath
     * @param string $noPictureImagePath
     * @param bool $isRtl
     * @param bool $isActive
     * @param int[] $shopAssociation
     */
    public function __construct(
        $name,
        $isoCode,
        $tagIETF,
        $shortDateFormat,
        $fullDateFormat,
        $flagImagePath,
        $noPictureImagePath,
        $isRtl,
        $isActive,
        array $shopAssociation
    ) {
        $this->name = $name;
        $this->isoCode = new IsoCode($isoCode);
        $this->tagIETF = new TagIETF($tagIETF);
        $this->shortDateFormat = $shortDateFormat;
        $this->fullDateFormat = $fullDateFormat;
        $this->flagImagePath = $flagImagePath;
        $this->noPictureImagePath = $noPictureImagePath;
        $this->isRtl = $isRtl;
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
     * @return string
     */
    public function getFlagImagePath()
    {
        return $this->flagImagePath;
    }

    /**
     * @return string
     */
    public function getNoPictureImagePath()
    {
        return $this->noPictureImagePath;
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
     * @return int[]
     */
    public function getShopAssociation()
    {
        return $this->shopAssociation;
    }
}
