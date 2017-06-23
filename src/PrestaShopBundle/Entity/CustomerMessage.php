<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerMessage
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_customer_thread", columns={"id_customer_thread"}), @ORM\Index(name="id_employee", columns={"id_employee"})})
 * @ORM\Entity
 */
class CustomerMessage
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_customer_thread", type="integer", nullable=true)
     */
    private $idCustomerThread;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_employee", type="integer", nullable=true)
     */
    private $idEmployee;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=16777215, nullable=false)
     */
    private $message;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=18, nullable=true)
     */
    private $fileName;

    /**
     * @var string
     *
     * @ORM\Column(name="ip_address", type="string", length=16, nullable=true)
     */
    private $ipAddress;

    /**
     * @var string
     *
     * @ORM\Column(name="user_agent", type="string", length=128, nullable=true)
     */
    private $userAgent;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_upd", type="datetime", nullable=false)
     */
    private $dateUpd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="private", type="boolean", nullable=false)
     */
    private $private = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="read", type="boolean", nullable=false)
     */
    private $read = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_customer_message", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCustomerMessage;



    /**
     * Set idCustomerThread
     *
     * @param integer $idCustomerThread
     *
     * @return CustomerMessage
     */
    public function setIdCustomerThread($idCustomerThread)
    {
        $this->idCustomerThread = $idCustomerThread;

        return $this;
    }

    /**
     * Get idCustomerThread
     *
     * @return integer
     */
    public function getIdCustomerThread()
    {
        return $this->idCustomerThread;
    }

    /**
     * Set idEmployee
     *
     * @param integer $idEmployee
     *
     * @return CustomerMessage
     */
    public function setIdEmployee($idEmployee)
    {
        $this->idEmployee = $idEmployee;

        return $this;
    }

    /**
     * Get idEmployee
     *
     * @return integer
     */
    public function getIdEmployee()
    {
        return $this->idEmployee;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return CustomerMessage
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return CustomerMessage
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get fileName
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * Set ipAddress
     *
     * @param string $ipAddress
     *
     * @return CustomerMessage
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;

        return $this;
    }

    /**
     * Get ipAddress
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set userAgent
     *
     * @param string $userAgent
     *
     * @return CustomerMessage
     */
    public function setUserAgent($userAgent)
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * Get userAgent
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return CustomerMessage
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * Get dateAdd
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set dateUpd
     *
     * @param \DateTime $dateUpd
     *
     * @return CustomerMessage
     */
    public function setDateUpd($dateUpd)
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    /**
     * Get dateUpd
     *
     * @return \DateTime
     */
    public function getDateUpd()
    {
        return $this->dateUpd;
    }

    /**
     * Set private
     *
     * @param boolean $private
     *
     * @return CustomerMessage
     */
    public function setPrivate($private)
    {
        $this->private = $private;

        return $this;
    }

    /**
     * Get private
     *
     * @return boolean
     */
    public function getPrivate()
    {
        return $this->private;
    }

    /**
     * Set read
     *
     * @param boolean $read
     *
     * @return CustomerMessage
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Get read
     *
     * @return boolean
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Get idCustomerMessage
     *
     * @return integer
     */
    public function getIdCustomerMessage()
    {
        return $this->idCustomerMessage;
    }
}
