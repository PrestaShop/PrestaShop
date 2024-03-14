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

namespace PrestaShop\PrestaShop\Core\Domain\Category\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;

/**
 * Holds valid value of category redirect type
 */
class RedirectType
{
    /**
     * Represents value of no redirection. Page not found (404) will be displayed.
     */
    public const TYPE_NOT_FOUND = '404';

    /**
     * Represents value of no redirection. Page gone (410) will be displayed.
     */
    public const TYPE_GONE = '410';

    /**
     * Represents value of permanent redirection to a category
     */
    public const TYPE_CATEGORY_PERMANENT = '301';

    /**
     * Represents value of temporary redirection to a category
     */
    public const TYPE_CATEGORY_TEMPORARY = '302';

    /**
     * Available redirection types
     */
    public const AVAILABLE_REDIRECT_TYPES = [
        self::TYPE_NOT_FOUND => self::TYPE_NOT_FOUND,
        self::TYPE_GONE => self::TYPE_GONE,
        self::TYPE_CATEGORY_PERMANENT => self::TYPE_CATEGORY_PERMANENT,
        self::TYPE_CATEGORY_TEMPORARY => self::TYPE_CATEGORY_TEMPORARY,
    ];

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $type
     *
     * @throws CategoryConstraintException
     */
    public function __construct(string $type)
    {
        $this->assertRedirectTypeIsAvailable($type);
        $this->value = $type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isTypeNotFound(): bool
    {
        return $this->getValue() === static::TYPE_NOT_FOUND;
    }

    /**
     * @return bool
     */
    public function isTypeGone(): bool
    {
        return $this->getValue() === static::TYPE_GONE;
    }

    /**
     * @return bool
     */
    public function isCategoryType(): bool
    {
        return in_array($this->value, [static::TYPE_CATEGORY_PERMANENT, static::TYPE_CATEGORY_TEMPORARY]);
    }

    /**
     * @param string $type
     *
     * @throws CategoryConstraintException
     */
    private function assertRedirectTypeIsAvailable(string $type): void
    {
        if (!in_array($type, static::AVAILABLE_REDIRECT_TYPES)) {
            throw new CategoryConstraintException(
                sprintf(
                    'Invalid redirect type "%s". Available redirect types are: %s',
                    $type,
                    implode(', ', static::AVAILABLE_REDIRECT_TYPES)
                ),
                CategoryConstraintException::INVALID_REDIRECT_TYPE
            );
        }
    }
}
