<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\Command;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;

/**
 * Updates the localized values overriding the product ones, an empty value restores the product one
 */
class UpdateCombinationContentCommand
{
    private CombinationId $combinationId;

    /**
     * @var string[]|null
     */
    private ?array $localizedDescriptions = null;

    /**
     * @var string[]|null
     */
    private ?array $localizedShortDescriptions = null;

    /**
     * @var string[]|null
     */
    private ?array $localizedLinkRewrites = null;

    /**
     * @var string[]|null
     */
    private ?array $localizedMetaDescriptions = null;

    /**
     * @var string[]|null
     */
    private ?array $localizedMetaTitles = null;

    public function __construct(
        int $combinationId,
        private readonly ShopConstraint $shopConstraint,
    ) {
        $this->combinationId = new CombinationId($combinationId);
    }

    public function getCombinationId(): CombinationId
    {
        return $this->combinationId;
    }

    public function getShopConstraint(): ShopConstraint
    {
        return $this->shopConstraint;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedDescriptions(): ?array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @param string[] $localizedDescriptions
     */
    public function setLocalizedDescriptions(array $localizedDescriptions): self
    {
        $this->localizedDescriptions = $localizedDescriptions;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedShortDescriptions(): ?array
    {
        return $this->localizedShortDescriptions;
    }

    /**
     * @param string[] $localizedShortDescriptions
     */
    public function setLocalizedShortDescriptions(array $localizedShortDescriptions): self
    {
        $this->localizedShortDescriptions = $localizedShortDescriptions;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedLinkRewrites(): ?array
    {
        return $this->localizedLinkRewrites;
    }

    /**
     * @param string[] $localizedLinkRewrites
     */
    public function setLocalizedLinkRewrites(array $localizedLinkRewrites): self
    {
        $this->localizedLinkRewrites = $localizedLinkRewrites;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaDescriptions(): ?array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @param string[] $localizedMetaDescriptions
     */
    public function setLocalizedMetaDescriptions(array $localizedMetaDescriptions): self
    {
        $this->localizedMetaDescriptions = $localizedMetaDescriptions;

        return $this;
    }

    /**
     * @return string[]|null
     */
    public function getLocalizedMetaTitles(): ?array
    {
        return $this->localizedMetaTitles;
    }

    /**
     * @param string[] $localizedMetaTitles
     */
    public function setLocalizedMetaTitles(array $localizedMetaTitles): self
    {
        $this->localizedMetaTitles = $localizedMetaTitles;

        return $this;
    }
}
