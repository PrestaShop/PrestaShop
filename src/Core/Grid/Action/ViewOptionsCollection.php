<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\Action;

use PrestaShop\PrestaShop\Core\Grid\Collection\AbstractCollection;

/**
 * Class ViewOptionsCollection is responsible for holding view options.
 *
 * @property array $items
 */
final class ViewOptionsCollection extends AbstractCollection implements ViewOptionsCollectionInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(string $action, $value)
    {
        $this->items[$action] = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        return $this->items;
    }
}
