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

namespace PrestaShop\PrestaShop\Adapter\Alias\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\BulkAliasException;
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;

/**
 * This abstract class helps us build a bulk handler since the principle is often very similar, it might not be
 * compatible with all the handlers, but it helps for many. The exact copy of Product Abstract Bulk Handler.
 */
abstract class AbstractBulkHandler
{
    /**
     * @param AliasId[] $aliasIds
     * @param mixed|null $command
     *
     * @return array<int, mixed>
     * @throws BulkAliasException
     */
    protected function handleBulkAction(array $aliasIds, $command = null): array
    {
        $bulkException = null;
        $actionResults = [];
        foreach ($aliasIds as $aliasId) {
            try {
                $actionResults[$aliasId->getValue()] = $this->handleSingleAction($aliasId, $command);
            } catch (BulkAliasException $e) {
                if (null === $bulkException) {
                    $bulkException = $this->buildBulkException();
                }
                $bulkException->addException($aliasId, $e);
            }
        }

        if (null !== $bulkException) {
            throw $bulkException;
        }

        return $actionResults;
    }

    /**
     * This uses the base bulk exception class, but you can override this in your handler.
     *
     * @return BulkAliasException
     */
    protected function buildBulkException(): BulkAliasException
    {
        return new BulkAliasException();
    }

    /**
     * @param AliasId $aliasId
     * @param mixed|null $command
     *
     * @return mixed
     */
    abstract protected function handleSingleAction(AliasId $aliasId, $command = null);
}
