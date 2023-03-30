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

namespace PrestaShop\PrestaShop\Adapter\CartRule\Repository;

use CartRule;
use PrestaShop\PrestaShop\Adapter\CartRule\Validate\CartRuleValidator;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CannotAddCartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CannotEditCartRuleException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\Exception\CartRuleNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;
use PrestaShop\PrestaShop\Core\Repository\AbstractObjectModelRepository;

class CartRuleRepository extends AbstractObjectModelRepository
{
    /**
     * @var CartRuleValidator
     */
    private $cartRuleValidator;

    public function __construct(
        CartRuleValidator $cartRuleValidator
    ) {
        $this->cartRuleValidator = $cartRuleValidator;
    }

    public function create(CartRule $cartRule): CartRule
    {
        $this->cartRuleValidator->validate($cartRule);
        $this->addObjectModel($cartRule, CannotAddCartRuleException::class);

        return $cartRule;
    }

    public function get(CartRuleId $cartRuleId): CartRule
    {
        /** @var CartRule $cartRule */
        $cartRule = $this->getObjectModel(
            $cartRuleId->getValue(),
            CartRule::class,
            CartRuleNotFoundException::class
        );

        return $cartRule;
    }

    /**
     * @param CartRule $cartRule
     * @param array<int|string, string|int[]> $propertiesToUpdate
     * @param int $errorCode
     */
    public function partialUpdate(CartRule $cartRule, array $propertiesToUpdate, int $errorCode = 0): void
    {
        //@todo: use validator when its merged in another PR. https://github.com/PrestaShop/PrestaShop/pull/31904
        $this->partiallyUpdateObjectModel(
            $cartRule,
            $propertiesToUpdate,
            CannotEditCartRuleException::class,
            $errorCode
        );
    }
}
