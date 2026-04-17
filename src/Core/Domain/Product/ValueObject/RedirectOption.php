<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;

/**
 * Holds valid redirect option data
 */
class RedirectOption
{
    /**
     * @var RedirectType
     */
    private $redirectType;

    /**
     * @var RedirectTarget
     */
    private $redirectTarget;

    /**
     * @param string $redirectType
     * @param int $redirectTarget
     */
    public function __construct(string $redirectType, int $redirectTarget)
    {
        $this->redirectType = new RedirectType($redirectType);
        $this->setRedirectTarget($redirectTarget);
        $this->assertTypeAndTargetIntegrity();
    }

    /**
     * @return RedirectType
     */
    public function getRedirectType(): RedirectType
    {
        return $this->redirectType;
    }

    /**
     * @return RedirectTarget
     */
    public function getRedirectTarget(): RedirectTarget
    {
        return $this->redirectTarget;
    }

    /**
     * @param int $value
     *
     * @throws ProductConstraintException
     */
    private function setRedirectTarget(int $value): void
    {
        if ($this->redirectType->isTypeNotFound()) {
            $value = RedirectTarget::NO_TARGET;
        }

        $this->redirectTarget = new RedirectTarget($value);
    }

    /**
     * Checks if redirect type is compatible with provided redirect target
     */
    private function assertTypeAndTargetIntegrity(): void
    {
        if ($this->redirectType->isProductType() && $this->redirectTarget->isNoTarget()) {
            throw new ProductConstraintException(
                sprintf(
                    'Invalid redirect target "%s". It must be a valid product id when type is "%s',
                    $this->redirectTarget->getValue(),
                    $this->redirectType->getValue()
                ),
                ProductConstraintException::INVALID_REDIRECT_TARGET
            );
        }
    }
}
