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
 * Customer
 *
 * @ORM\Table(indexes={@ORM\Index(name="customer_email", columns={"email"}), @ORM\Index(name="customer_login", columns={"email", "passwd"}), @ORM\Index(name="id_customer_passwd", columns={"id_customer", "passwd"}), @ORM\Index(name="id_gender", columns={"id_gender"}), @ORM\Index(name="id_shop_group", columns={"id_shop_group"}), @ORM\Index(name="id_shop", columns={"id_shop", "date_add"})})
 * @ORM\Entity
 */
class Customer
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop_group", type="integer", nullable=false, options={"default":1})
     */
    private $idShopGroup = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_shop", type="integer", nullable=false, options={"default":1})
     */
    private $idShop = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_gender", type="integer", nullable=false)
     */
    private $idGender;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_default_group", type="integer", nullable=false, options={"default":1})
     */
    private $idDefaultGroup = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer", nullable=true)
     */
    private $idLang;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_risk", type="integer", nullable=false, options={"default":1})
     */
    private $idRisk = '1';

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=64, nullable=true)
     */
    private $company;

    /**
     * @var string
     *
     * @ORM\Column(name="siret", type="string", length=14, nullable=true)
     */
    private $siret;

    /**
     * @var string
     *
     * @ORM\Column(name="ape", type="string", length=5, nullable=true)
     */
    private $ape;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255, nullable=false)
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255, nullable=false)
     */
    private $lastname;

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
     * @ORM\Column(name="birthday", type="date", nullable=true)
     */
    private $birthday;

    /**
     * @var boolean
     *
     * @ORM\Column(name="newsletter", type="boolean", nullable=false, options={"default":0})
     */
    private $newsletter = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="ip_registration_newsletter", type="string", length=15, nullable=true)
     */
    private $ipRegistrationNewsletter;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="newsletter_date_add", type="datetime", nullable=true)
     */
    private $newsletterDateAdd;

    /**
     * @var boolean
     *
     * @ORM\Column(name="optin", type="boolean", nullable=false, options={"default":0})
     */
    private $optin = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=128, nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="outstanding_allow_amount", type="decimal", precision=20, scale=6, nullable=false, options={"default":0.000000})
     */
    private $outstandingAllowAmount = '0.000000';

    /**
     * @var boolean
     *
     * @ORM\Column(name="show_public_prices", type="boolean", nullable=false, options={"default":0})
     */
    private $showPublicPrices = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="max_payment_days", type="integer", nullable=false, options={"default":60})
     */
    private $maxPaymentDays = '60';

    /**
     * @var string
     *
     * @ORM\Column(name="secure_key", type="string", length=32, nullable=false, options={"default":-1})
     */
    private $secureKey = '-1';

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", length=65535, nullable=true)
     */
    private $note;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":0})
     */
    private $active = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_guest", type="boolean", nullable=false, options={"default":0})
     */
    private $isGuest = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false, options={"default":0})
     */
    private $deleted = '0';

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
     * @ORM\Column(name="id_customer", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCustomer;



    /**
     * Set idShopGroup
     *
     * @param integer $idShopGroup
     *
     * @return Customer
     */
    public function setIdShopGroup($idShopGroup)
    {
        $this->idShopGroup = $idShopGroup;

        return $this;
    }

    /**
     * Get idShopGroup
     *
     * @return integer
     */
    public function getIdShopGroup()
    {
        return $this->idShopGroup;
    }

    /**
     * Set idShop
     *
     * @param integer $idShop
     *
     * @return Customer
     */
    public function setIdShop($idShop)
    {
        $this->idShop = $idShop;

        return $this;
    }

    /**
     * Get idShop
     *
     * @return integer
     */
    public function getIdShop()
    {
        return $this->idShop;
    }

    /**
     * Set idGender
     *
     * @param integer $idGender
     *
     * @return Customer
     */
    public function setIdGender($idGender)
    {
        $this->idGender = $idGender;

        return $this;
    }

    /**
     * Get idGender
     *
     * @return integer
     */
    public function getIdGender()
    {
        return $this->idGender;
    }

    /**
     * Set idDefaultGroup
     *
     * @param integer $idDefaultGroup
     *
     * @return Customer
     */
    public function setIdDefaultGroup($idDefaultGroup)
    {
        $this->idDefaultGroup = $idDefaultGroup;

        return $this;
    }

    /**
     * Get idDefaultGroup
     *
     * @return integer
     */
    public function getIdDefaultGroup()
    {
        return $this->idDefaultGroup;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return Customer
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
     * Set idRisk
     *
     * @param integer $idRisk
     *
     * @return Customer
     */
    public function setIdRisk($idRisk)
    {
        $this->idRisk = $idRisk;

        return $this;
    }

    /**
     * Get idRisk
     *
     * @return integer
     */
    public function getIdRisk()
    {
        return $this->idRisk;
    }

    /**
     * Set company
     *
     * @param string $company
     *
     * @return Customer
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set siret
     *
     * @param string $siret
     *
     * @return Customer
     */
    public function setSiret($siret)
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * Get siret
     *
     * @return string
     */
    public function getSiret()
    {
        return $this->siret;
    }

    /**
     * Set ape
     *
     * @param string $ape
     *
     * @return Customer
     */
    public function setApe($ape)
    {
        $this->ape = $ape;

        return $this;
    }

    /**
     * Get ape
     *
     * @return string
     */
    public function getApe()
    {
        return $this->ape;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return Customer
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
     * Set lastname
     *
     * @param string $lastname
     *
     * @return Customer
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
     * Set email
     *
     * @param string $email
     *
     * @return Customer
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
     * @return Customer
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
     * @return Customer
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
     * Set birthday
     *
     * @param \DateTime $birthday
     *
     * @return Customer
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Get birthday
     *
     * @return \DateTime
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Set newsletter
     *
     * @param boolean $newsletter
     *
     * @return Customer
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * Get newsletter
     *
     * @return boolean
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Set ipRegistrationNewsletter
     *
     * @param string $ipRegistrationNewsletter
     *
     * @return Customer
     */
    public function setIpRegistrationNewsletter($ipRegistrationNewsletter)
    {
        $this->ipRegistrationNewsletter = $ipRegistrationNewsletter;

        return $this;
    }

    /**
     * Get ipRegistrationNewsletter
     *
     * @return string
     */
    public function getIpRegistrationNewsletter()
    {
        return $this->ipRegistrationNewsletter;
    }

    /**
     * Set newsletterDateAdd
     *
     * @param \DateTime $newsletterDateAdd
     *
     * @return Customer
     */
    public function setNewsletterDateAdd($newsletterDateAdd)
    {
        $this->newsletterDateAdd = $newsletterDateAdd;

        return $this;
    }

    /**
     * Get newsletterDateAdd
     *
     * @return \DateTime
     */
    public function getNewsletterDateAdd()
    {
        return $this->newsletterDateAdd;
    }

    /**
     * Set optin
     *
     * @param boolean $optin
     *
     * @return Customer
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
     * Set website
     *
     * @param string $website
     *
     * @return Customer
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set outstandingAllowAmount
     *
     * @param string $outstandingAllowAmount
     *
     * @return Customer
     */
    public function setOutstandingAllowAmount($outstandingAllowAmount)
    {
        $this->outstandingAllowAmount = $outstandingAllowAmount;

        return $this;
    }

    /**
     * Get outstandingAllowAmount
     *
     * @return string
     */
    public function getOutstandingAllowAmount()
    {
        return $this->outstandingAllowAmount;
    }

    /**
     * Set showPublicPrices
     *
     * @param boolean $showPublicPrices
     *
     * @return Customer
     */
    public function setShowPublicPrices($showPublicPrices)
    {
        $this->showPublicPrices = $showPublicPrices;

        return $this;
    }

    /**
     * Get showPublicPrices
     *
     * @return boolean
     */
    public function getShowPublicPrices()
    {
        return $this->showPublicPrices;
    }

    /**
     * Set maxPaymentDays
     *
     * @param integer $maxPaymentDays
     *
     * @return Customer
     */
    public function setMaxPaymentDays($maxPaymentDays)
    {
        $this->maxPaymentDays = $maxPaymentDays;

        return $this;
    }

    /**
     * Get maxPaymentDays
     *
     * @return integer
     */
    public function getMaxPaymentDays()
    {
        return $this->maxPaymentDays;
    }

    /**
     * Set secureKey
     *
     * @param string $secureKey
     *
     * @return Customer
     */
    public function setSecureKey($secureKey)
    {
        $this->secureKey = $secureKey;

        return $this;
    }

    /**
     * Get secureKey
     *
     * @return string
     */
    public function getSecureKey()
    {
        return $this->secureKey;
    }

    /**
     * Set note
     *
     * @param string $note
     *
     * @return Customer
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Customer
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
     * Set isGuest
     *
     * @param boolean $isGuest
     *
     * @return Customer
     */
    public function setIsGuest($isGuest)
    {
        $this->isGuest = $isGuest;

        return $this;
    }

    /**
     * Get isGuest
     *
     * @return boolean
     */
    public function getIsGuest()
    {
        return $this->isGuest;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return Customer
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Customer
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
     * @return Customer
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
     * Set resetPasswordToken
     *
     * @param string $resetPasswordToken
     *
     * @return Customer
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
     * @return Customer
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
     * Get idCustomer
     *
     * @return integer
     */
    public function getIdCustomer()
    {
        return $this->idCustomer;
    }
}
