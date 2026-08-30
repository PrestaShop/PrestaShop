<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateConstraintException;

/**
 * Adds new order return state with provided data
 */
class AddOrderReturnStateCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;

    public function __construct(
        array $localizedNames,
        private string $color,
        private bool $isCancellingReturn = false
    ) {
        $this->setLocalizedNames($localizedNames);
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws OrderReturnStateConstraintException
     */
    public function setLocalizedNames(array $localizedNames)
    {
        if (empty($localizedNames)) {
            throw new OrderReturnStateConstraintException('Order return status name cannot be empty', OrderReturnStateConstraintException::EMPTY_NAME);
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    public function setCancellingReturn(bool $isCancellingReturn): self
    {
        $this->isCancellingReturn = $isCancellingReturn;

        return $this;
    }

    public function isCancellingReturn(): bool
    {
        return $this->isCancellingReturn;
    }
}
