<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Category\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;

/**
 * Holds valid redirect option data
 */
class RedirectOption
{
    private RedirectType $redirectType;
    private RedirectTarget $redirectTarget;

    /**
     * @throws CategoryConstraintException
     */
    public function __construct(string $redirectType, int $redirectTarget)
    {
        $this->redirectType = new RedirectType($redirectType);
        $this->setRedirectTarget($redirectTarget);
    }

    public function getRedirectType(): RedirectType
    {
        return $this->redirectType;
    }

    public function getRedirectTarget(): RedirectTarget
    {
        return $this->redirectTarget;
    }

    /**
     * @throws CategoryConstraintException
     */
    private function setRedirectTarget(int $value): void
    {
        if ($this->redirectType->isTypeNotFound()) {
            $value = RedirectTarget::NO_TARGET;
        }

        $this->redirectTarget = new RedirectTarget($value);
    }
}
