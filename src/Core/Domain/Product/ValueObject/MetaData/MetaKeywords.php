<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\MetaData;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Product keywords metadata.
 */
final class MetaKeywords
{
    public const MAX_SIZE = 255;

    /**
     * @var string
     */
    private $metaKeywords;

    public function __construct(string $metaKeywords)
    {
        $this->setMetaKeywords($metaKeywords);
    }

    public function getValue(): string
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $name
     *
     * @throws ProductConstraintException
     */
    private function setMetaKeywords(string $name): void
    {
        $pattern = '/^[^<>={}]*$/u';

        if (!preg_match($pattern, $name)) {
            throw new ProductConstraintException(
                sprintf(
                    'Given product meta keywords "%s" did not matched pattern "%s"',
                    $name,
                    $pattern
                ),
                ProductConstraintException::INVALID_META_KEYWORDS
            );
        }

        if (strlen($name) > self::MAX_SIZE) {
            throw new ProductConstraintException(
                sprintf(
                    'Given product meta keywords "%s" is longer then expected size %d',
                    $name,
                    self::MAX_SIZE
                ),
                ProductConstraintException::META_KEYWORDS_NAME_TOO_LONG
            );
        }

        $this->metaKeywords = $name;
    }
}
