<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
    public const TYPE_PERMANENT = '301';

    /**
     * Represents value of temporary redirection to a category
     */
    public const TYPE_TEMPORARY = '302';

    /**
     * Available redirection types
     */
    public const AVAILABLE_REDIRECT_TYPES = [
        self::TYPE_NOT_FOUND => self::TYPE_NOT_FOUND,
        self::TYPE_GONE => self::TYPE_GONE,
        self::TYPE_PERMANENT => self::TYPE_PERMANENT,
        self::TYPE_TEMPORARY => self::TYPE_TEMPORARY,
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
        return in_array($this->value, [static::TYPE_PERMANENT, static::TYPE_TEMPORARY]);
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
