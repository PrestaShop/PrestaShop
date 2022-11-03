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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
