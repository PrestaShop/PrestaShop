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

namespace PrestaShop\PrestaShop\Core\Grid\Collection;

use Countable;
use Iterator;

/**
 * Class AbstractCollection is responsible for providing base collection implementation.
 */
abstract class AbstractCollection implements Iterator, Countable
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        return current($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function next(): void
    {
        next($this->items);
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return key($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function valid(): bool
    {
        return false !== $this->current();
    }

    /**
     * {@inheritdoc}
     */
    public function rewind(): void
    {
        reset($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return count($this->items);
    }
}
