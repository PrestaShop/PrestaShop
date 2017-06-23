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
 * Employee
 *
 * @ORM\Table(indexes={@ORM\Index(name="employee_login", columns={"email", "passwd"}), @ORM\Index(name="id_employee_passwd", columns={"id_employee", "passwd"}), @ORM\Index(name="id_profile", columns={"id_profile"})})
 * @ORM\Entity
 */
class Employee
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_profile", type="integer", nullable=false)
     */
    private $idProfile;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer", nullable=false)
     */
    private $idLang = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=32, nullable=false)
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=32, nullable=false)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=128, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="passwd", type="string", length=60, nullable=false)
     */
    private $passwd;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_passwd_gen", type="datetime", nullable=false)
     */
    private $lastPasswdGen = 'CURRENT_TIMESTAMP';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stats_date_from", type="date", nullable=true)
     */
    private $statsDateFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stats_date_to", type="date", nullable=true)
     */
    private $statsDateTo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stats_compare_from", type="date", nullable=true)
     */
    private $statsCompareFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="stats_compare_to", type="date", nullable=true)
     */
    private $statsCompareTo;

    /**
     * @var integer
     *
     * @ORM\Column(name="stats_compare_option", type="integer", nullable=false)
     */
    private $statsCompareOption = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="preselect_date_range", type="string", length=32, nullable=true)
     */
    private $preselectDateRange;

    /**
     * @var string
     *
     * @ORM\Column(name="bo_color", type="string", length=32, nullable=true)
     */
    private $boColor;

    /**
     * @var string
     *
     * @ORM\Column(name="bo_theme", type="string", length=32, nullable=true)
     */
    private $boTheme;

    /**
     * @var string
     *
     * @ORM\Column(name="bo_css", type="string", length=64, nullable=true)
     */
    private $boCss;

    /**
     * @var integer
     *
     * @ORM\Column(name="default_tab", type="integer", nullable=false)
     */
    private $defaultTab = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="bo_width", type="integer", nullable=false)
     */
    private $boWidth = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="bo_menu", type="boolean", nullable=false)
     */
    private $boMenu = '1';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="optin", type="boolean", nullable=false)
     */
    private $optin = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_last_order", type="integer", nullable=false)
     */
    private $idLastOrder = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_last_customer_message", type="integer", nullable=false)
     */
    private $idLastCustomerMessage = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_last_customer", type="integer", nullable=false)
     */
    private $idLastCustomer = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_connection_date", type="date", nullable=true)
     */
    private $lastConnectionDate;

    /**
     * @var string
     *
     * @ORM\Column(name="reset_password_token", type="string", length=40, nullable=true)
     */
    private $resetPasswordToken;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="reset_password_validity", type="datetime", nullable=true)
     */
    private $resetPasswordValidity;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_employee", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idEmployee;



    /**
     * Set idProfile
     *
     * @param integer $idProfile
     *
     * @return Employee
     */
    public function setIdProfile($idProfile)
    {
        $this->idProfile = $idProfile;

        return $this;
    }

    /**
     * Get idProfile
     *
     * @return integer
     */
    public function getIdProfile()
    {
        return $this->idProfile;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return Employee
     */
    public function setIdLang($idLang)
    {
        $this->idLang = $idLang;

        return $this;
    }

    /**
     * Get idLang
     *
     * @return integer
     */
    public function getIdLang()
    {
        return $this->idLang;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Employee
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Employee
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Employee
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set passwd
     *
     * @param string $passwd
     *
     * @return Employee
     */
    public function setPasswd($passwd)
    {
        $this->passwd = $passwd;

        return $this;
    }

    /**
     * Get passwd
     *
     * @return string
     */
    public function getPasswd()
    {
        return $this->passwd;
    }

    /**
     * Set lastPasswdGen
     *
     * @param \DateTime $lastPasswdGen
     *
     * @return Employee
     */
    public function setLastPasswdGen($lastPasswdGen)
    {
        $this->lastPasswdGen = $lastPasswdGen;

        return $this;
    }

    /**
     * Get lastPasswdGen
     *
     * @return \DateTime
     */
    public function getLastPasswdGen()
    {
        return $this->lastPasswdGen;
    }

    /**
     * Set statsDateFrom
     *
     * @param \DateTime $statsDateFrom
     *
     * @return Employee
     */
    public function setStatsDateFrom($statsDateFrom)
    {
        $this->statsDateFrom = $statsDateFrom;

        return $this;
    }

    /**
     * Get statsDateFrom
     *
     * @return \DateTime
     */
    public function getStatsDateFrom()
    {
        return $this->statsDateFrom;
    }

    /**
     * Set statsDateTo
     *
     * @param \DateTime $statsDateTo
     *
     * @return Employee
     */
    public function setStatsDateTo($statsDateTo)
    {
        $this->statsDateTo = $statsDateTo;

        return $this;
    }

    /**
     * Get statsDateTo
     *
     * @return \DateTime
     */
    public function getStatsDateTo()
    {
        return $this->statsDateTo;
    }

    /**
     * Set statsCompareFrom
     *
     * @param \DateTime $statsCompareFrom
     *
     * @return Employee
     */
    public function setStatsCompareFrom($statsCompareFrom)
    {
        $this->statsCompareFrom = $statsCompareFrom;

        return $this;
    }

    /**
     * Get statsCompareFrom
     *
     * @return \DateTime
     */
    public function getStatsCompareFrom()
    {
        return $this->statsCompareFrom;
    }

    /**
     * Set statsCompareTo
     *
     * @param \DateTime $statsCompareTo
     *
     * @return Employee
     */
    public function setStatsCompareTo($statsCompareTo)
    {
        $this->statsCompareTo = $statsCompareTo;

        return $this;
    }

    /**
     * Get statsCompareTo
     *
     * @return \DateTime
     */
    public function getStatsCompareTo()
    {
        return $this->statsCompareTo;
    }

    /**
     * Set statsCompareOption
     *
     * @param integer $statsCompareOption
     *
     * @return Employee
     */
    public function setStatsCompareOption($statsCompareOption)
    {
        $this->statsCompareOption = $statsCompareOption;

        return $this;
    }

    /**
     * Get statsCompareOption
     *
     * @return integer
     */
    public function getStatsCompareOption()
    {
        return $this->statsCompareOption;
    }

    /**
     * Set preselectDateRange
     *
     * @param string $preselectDateRange
     *
     * @return Employee
     */
    public function setPreselectDateRange($preselectDateRange)
    {
        $this->preselectDateRange = $preselectDateRange;

        return $this;
    }

    /**
     * Get preselectDateRange
     *
     * @return string
     */
    public function getPreselectDateRange()
    {
        return $this->preselectDateRange;
    }

    /**
     * Set boColor
     *
     * @param string $boColor
     *
     * @return Employee
     */
    public function setBoColor($boColor)
    {
        $this->boColor = $boColor;

        return $this;
    }

    /**
     * Get boColor
     *
     * @return string
     */
    public function getBoColor()
    {
        return $this->boColor;
    }

    /**
     * Set boTheme
     *
     * @param string $boTheme
     *
     * @return Employee
     */
    public function setBoTheme($boTheme)
    {
        $this->boTheme = $boTheme;

        return $this;
    }

    /**
     * Get boTheme
     *
     * @return string
     */
    public function getBoTheme()
    {
        return $this->boTheme;
    }

    /**
     * Set boCss
     *
     * @param string $boCss
     *
     * @return Employee
     */
    public function setBoCss($boCss)
    {
        $this->boCss = $boCss;

        return $this;
    }

    /**
     * Get boCss
     *
     * @return string
     */
    public function getBoCss()
    {
        return $this->boCss;
    }

    /**
     * Set defaultTab
     *
     * @param integer $defaultTab
     *
     * @return Employee
     */
    public function setDefaultTab($defaultTab)
    {
        $this->defaultTab = $defaultTab;

        return $this;
    }

    /**
     * Get defaultTab
     *
     * @return integer
     */
    public function getDefaultTab()
    {
        return $this->defaultTab;
    }

    /**
     * Set boWidth
     *
     * @param integer $boWidth
     *
     * @return Employee
     */
    public function setBoWidth($boWidth)
    {
        $this->boWidth = $boWidth;

        return $this;
    }

    /**
     * Get boWidth
     *
     * @return integer
     */
    public function getBoWidth()
    {
        return $this->boWidth;
    }

    /**
     * Set boMenu
     *
     * @param boolean $boMenu
     *
     * @return Employee
     */
    public function setBoMenu($boMenu)
    {
        $this->boMenu = $boMenu;

        return $this;
    }

    /**
     * Get boMenu
     *
     * @return boolean
     */
    public function getBoMenu()
    {
        return $this->boMenu;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Employee
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * Set optin
     *
     * @param boolean $optin
     *
     * @return Employee
     */
    public function setOptin($optin)
    {
        $this->optin = $optin;

        return $this;
    }

    /**
     * Get optin
     *
     * @return boolean
     */
    public function getOptin()
    {
        return $this->optin;
    }

    /**
     * Set idLastOrder
     *
     * @param integer $idLastOrder
     *
     * @return Employee
     */
    public function setIdLastOrder($idLastOrder)
    {
        $this->idLastOrder = $idLastOrder;

        return $this;
    }

    /**
     * Get idLastOrder
     *
     * @return integer
     */
    public function getIdLastOrder()
    {
        return $this->idLastOrder;
    }

    /**
     * Set idLastCustomerMessage
     *
     * @param integer $idLastCustomerMessage
     *
     * @return Employee
     */
    public function setIdLastCustomerMessage($idLastCustomerMessage)
    {
        $this->idLastCustomerMessage = $idLastCustomerMessage;

        return $this;
    }

    /**
     * Get idLastCustomerMessage
     *
     * @return integer
     */
    public function getIdLastCustomerMessage()
    {
        return $this->idLastCustomerMessage;
    }

    /**
     * Set idLastCustomer
     *
     * @param integer $idLastCustomer
     *
     * @return Employee
     */
    public function setIdLastCustomer($idLastCustomer)
    {
        $this->idLastCustomer = $idLastCustomer;

        return $this;
    }

    /**
     * Get idLastCustomer
     *
     * @return integer
     */
    public function getIdLastCustomer()
    {
        return $this->idLastCustomer;
    }

    /**
     * Set lastConnectionDate
     *
     * @param \DateTime $lastConnectionDate
     *
     * @return Employee
     */
    public function setLastConnectionDate($lastConnectionDate)
    {
        $this->lastConnectionDate = $lastConnectionDate;

        return $this;
    }

    /**
     * Get lastConnectionDate
     *
     * @return \DateTime
     */
    public function getLastConnectionDate()
    {
        return $this->lastConnectionDate;
    }

    /**
     * Set resetPasswordToken
     *
     * @param string $resetPasswordToken
     *
     * @return Employee
     */
    public function setResetPasswordToken($resetPasswordToken)
    {
        $this->resetPasswordToken = $resetPasswordToken;

        return $this;
    }

    /**
     * Get resetPasswordToken
     *
     * @return string
     */
    public function getResetPasswordToken()
    {
        return $this->resetPasswordToken;
    }

    /**
     * Set resetPasswordValidity
     *
     * @param \DateTime $resetPasswordValidity
     *
     * @return Employee
     */
    public function setResetPasswordValidity($resetPasswordValidity)
    {
        $this->resetPasswordValidity = $resetPasswordValidity;

        return $this;
    }

    /**
     * Get resetPasswordValidity
     *
     * @return \DateTime
     */
    public function getResetPasswordValidity()
    {
        return $this->resetPasswordValidity;
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
}
