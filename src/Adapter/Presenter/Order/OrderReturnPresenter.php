<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Order;

use Exception;
use Hook;
use Link;
use PrestaShop\PrestaShop\Adapter\Presenter\PresenterInterface;
use ReflectionException;

class OrderReturnPresenter implements PresenterInterface
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var Link
     */
    private $link;

    /**
     * OrderReturnPresenter constructor.
     *
     * @param string $prefix
     * @param Link $link
     */
    public function __construct($prefix, Link $link)
    {
        $this->prefix = $prefix;
        $this->link = $link;
    }

    /**
     * @param array $orderReturn
     *
     * @return OrderReturnLazyArray
     *
     * @throws ReflectionException
     */
    public function present($orderReturn)
    {
        if (!is_array($orderReturn)) {
            throw new Exception('orderReturnPresenter can only present order_return passed as array');
        }

        $orderReturnLazyArray = new OrderReturnLazyArray($this->prefix, $this->link, $orderReturn);

        Hook::exec('actionPresentOrderReturn',
            ['presentedOrderReturn' => &$orderReturnLazyArray]
        );

        return $orderReturnLazyArray;
    }
}
