<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Core\Session\SessionInterface;

class CustomerSessionCore extends ObjectModel implements SessionInterface
{
    public $id;

    /** @var int Id Customer */
    public $id_customer;

    /** @var string Token */
    public $token;

    /** @var string Object last modification date */
    public $date_upd;

    /** @var string Object creation date */
    public $date_add;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'customer_session',
        'primary' => 'id_customer_session',
        'fields' => [
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true],
            'token' => ['type' => self::TYPE_STRING, 'validate' => 'isSha1', 'size' => 40, 'copy_post' => false],
            'date_upd' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false],
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setUserId($idCustomer)
    {
        $this->id_customer = (int) $idCustomer;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserId()
    {
        return (int) $this->id_customer;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken($token)
    {
        $this->token = (string) $token;
    }

    /**
     * {@inheritdoc}
     */
    public function getToken()
    {
        return $this->token;
    }
}
