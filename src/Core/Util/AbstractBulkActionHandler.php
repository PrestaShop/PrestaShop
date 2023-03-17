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

namespace PrestaShop\PrestaShop\Core\Util;

use Exception;
use PrestaShop\PrestaShop\Core\Exception\BulkActionExceptionInterface;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use Throwable;

abstract class AbstractBulkActionHandler
{
    /**
     * @var Exception[]
     */
    protected $exceptions;

    /**
     * @param array $ids
     * @param string|null $exceptionToCatch when cought this exception will allow the loop to continue
     *                                      and show bulk error at the end of the loop, instead of breaking it on first error.
     *                                      When NULL is provided, then loop will stop on first error.
     *
     * @throws BulkActionExceptionInterface
     */
    protected function handleBulkAction(array $ids, ?string $exceptionToCatch = null): void
    {
        foreach ($ids as $id) {
            try {
                if (!$this->supports($id)) {
                    throw new InvalidArgumentException(
                        sprintf('%s not supported by bulk action', var_export($id, true))
                    );
                }
                $this->handleSingleAction($id);
            } catch (Throwable $e) {
                if (null === $exceptionToCatch) {
                    throw $e;
                }

                if (!($e instanceof $exceptionToCatch)) {
                    throw $e;
                }

                $this->exceptions[] = $e;
            }
        }

        if (!empty($this->exceptions)) {
            throw $this->buildBulkException($this->exceptions);
        }
    }

    /**
     * @param Throwable[] $coughtExceptions
     *
     * @return BulkActionExceptionInterface
     */
    abstract protected function buildBulkException(array $coughtExceptions): BulkActionExceptionInterface;

    /**
     * @param mixed $id
     */
    abstract protected function handleSingleAction($id): void;

    /**
     * Should return true if provided $id type is supported by actions, false otherwise
     *
     * @param mixed $id
     *
     * @return bool
     */
    abstract protected function supports($id): bool;
}
