<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Order;

use Link;
use PrestaShop\PrestaShop\Adapter\Presenter\AbstractLazyArray;
use PrestaShop\PrestaShop\Adapter\Presenter\LazyArrayAttribute;
use PrestaShopException;
use ReflectionException;
use Tools;

class OrderReturnLazyArray extends AbstractLazyArray
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var Link
     */
    private $link;

    /** @var array */
    private $orderReturn;

    /**
     * OrderReturnLazyArray constructor.
     *
     * @param string $prefix
     * @param Link $link
     * @param array $orderReturn
     *
     * @throws ReflectionException
     */
    public function __construct($prefix, Link $link, array $orderReturn)
    {
        $this->prefix = $prefix;
        $this->link = $link;
        $this->orderReturn = $orderReturn;
        parent::__construct();
        $this->appendArray($orderReturn);
    }

    /**
     * @return mixed
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getId()
    {
        return $this->orderReturn['id_order_return'];
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getDetailsUrl()
    {
        return $this->link->getPageLink(
            'order-detail',
            null,
            null,
            'id_order=' . (int) $this->orderReturn['id_order']
        );
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getReturnUrl()
    {
        return $this->link->getPageLink(
            'order-return',
            null,
            null,
            'id_order_return=' . (int) $this->orderReturn['id_order_return']
        );
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getReturnNumber()
    {
        return $this->prefix . sprintf('%06d', $this->orderReturn['id_order_return']);
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getReturnDate()
    {
        return Tools::displayDate($this->orderReturn['date_add'], false);
    }

    /**
     * @return string
     */
    #[LazyArrayAttribute(arrayAccess: true)]
    public function getPrintUrl()
    {
        return ($this->orderReturn['state'] == 2)
            ? $this->link->getPageLink(
                'pdf-order-return',
                null,
                null,
                'id_order_return=' . (int) $this->orderReturn['id_order_return']
            )
            : '';
    }
}
