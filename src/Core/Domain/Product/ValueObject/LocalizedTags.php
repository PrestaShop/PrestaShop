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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    public const VALID_TAG_PATTERN = '/^[^<>={}]*$/u';

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
            //skip empty value
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
