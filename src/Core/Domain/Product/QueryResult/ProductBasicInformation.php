<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Contains some basic information about product
 */
class ProductBasicInformation
{
    /**
     * @var string[]
     */
    private $localizedNames;

    /**
     * @var string[]
     */
    private $localizedDescriptions;

    /**
     * @var string[]
     */
    private $localizedShortDescriptions;

    /**
     * @var LocalizedTags[]
     */
    private $localizedTags;

    /**
     * @param string[] $localizedNames
     * @param string[] $localizedDescriptions
     * @param string[] $localizedShortDescriptions
     * @param LocalizedTags[] $localizedTags
     */
    public function __construct(
        array $localizedNames,
        array $localizedDescriptions,
        array $localizedShortDescriptions,
        array $localizedTags
    ) {
        $this->localizedNames = $localizedNames;
        $this->localizedDescriptions = $localizedDescriptions;
        $this->localizedShortDescriptions = $localizedShortDescriptions;
        $this->localizedTags = $localizedTags;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
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
     * @return LocalizedTags[]
     */
    public function getLocalizedTags(): array
    {
        return $this->localizedTags;
    }
}
