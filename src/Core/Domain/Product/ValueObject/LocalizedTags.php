<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Language\ValueObject\LanguageId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds valid list of product tags in one language
 */
class LocalizedTags
{
    public const VALID_TAG_PATTERN = '/^[^<>{}]*$/u';

    /**
     * @var LanguageId
     */
    private $languageId;

    /**
     * @var string[]
     */
    private $tags;

    /**
     * @param int $langId
     * @param string[] $tags
     *
     * @throws ProductConstraintException
     */
    public function __construct(int $langId, array $tags)
    {
        $this->languageId = new LanguageId($langId);
        $this->setTags($tags);
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->tags);
    }

    /**
     * @return LanguageId
     */
    public function getLanguageId(): LanguageId
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

    /**
     * @param array $tags
     *
     * @throws ProductConstraintException
     */
    private function setTags(array $tags): void
    {
        $this->tags = [];

        foreach ($tags as $tag) {
            // skip empty value
            if (empty($tag)) {
                continue;
            }

            $this->assertTagIsValid($tag);
            $this->tags[] = $tag;
        }
    }

    /**
     * @param string $tag
     *
     * @throws ProductConstraintException
     */
    private function assertTagIsValid(string $tag): void
    {
        if (!preg_match(self::VALID_TAG_PATTERN, $tag)) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid product tag "%s" in language with id "%s"',
                    $tag,
                    $this->languageId->getValue()
                ),
                ProductConstraintException::INVALID_TAG
            );
        }
    }
}
