<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Order\Exception;

/**
 * Thrown when the name given for a discount added to an order is longer than the column can hold.
 * Carries both lengths so the message shown to the employee can name them.
 */
class CartRuleNameTooLongException extends OrderConstraintException
{
    /**
     * @var int
     */
    private $givenLength;

    /**
     * @var int
     */
    private $maxLength;

    public function __construct(int $givenLength, int $maxLength)
    {
        parent::__construct(
            sprintf('Cart rule name is %d characters long, it must be at most %d.', $givenLength, $maxLength),
            OrderConstraintException::INVALID_CART_RULE_NAME
        );

        $this->givenLength = $givenLength;
        $this->maxLength = $maxLength;
    }

    public function getGivenLength(): int
    {
        return $this->givenLength;
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }
}
