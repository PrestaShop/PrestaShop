<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\QueryResult;

/**
 * Holds data of product tags in single language
 */
class LocalizedTags
{
    /**
     * @var int
     */
    private $languageId;

    /**
     * @var string[]
     */
    private $tags;

    /**
     * @param int $languageId
     * @param string[] $tags
     */
    public function __construct(int $languageId, array $tags)
    {
        $this->languageId = $languageId;
        $this->tags = $tags;
    }

    /**
     * @return int
     */
    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
