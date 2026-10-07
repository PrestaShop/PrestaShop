<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\QueryResult;

/**
 * Localized values overriding the product ones, empty when the product value is used
 */
final class CombinationContent
{
    /**
     * @param string[] $localizedDescriptions
     * @param string[] $localizedShortDescriptions
     * @param string[] $localizedLinkRewrites
     * @param string[] $localizedMetaDescriptions
     * @param string[] $localizedMetaTitles
     */
    public function __construct(
        private readonly array $localizedDescriptions,
        private readonly array $localizedShortDescriptions,
        private readonly array $localizedLinkRewrites,
        private readonly array $localizedMetaDescriptions,
        private readonly array $localizedMetaTitles,
    ) {
    }

    /**
     * @return string[]
     */
    public function getLocalizedDescriptions(): array
    {
        return $this->localizedDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedShortDescriptions(): array
    {
        return $this->localizedShortDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedLinkRewrites(): array
    {
        return $this->localizedLinkRewrites;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaDescriptions(): array
    {
        return $this->localizedMetaDescriptions;
    }

    /**
     * @return string[]
     */
    public function getLocalizedMetaTitles(): array
    {
        return $this->localizedMetaTitles;
    }
}
