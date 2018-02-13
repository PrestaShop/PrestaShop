<?php
/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */


namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AttributeLang
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\AttributeLangRepository")
 */
class AttributeLang
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Attribute", inversedBy="attributeLangs")
     * @ORM\JoinColumn(name="id_attribute", referencedColumnName="id_attribute", nullable=false)
     */
    private $attribute;

    /**
     * @ORM\Id
     * @ORM\Column(name="id_lang", type="integer")
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=128)
     */
    private $name;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->attribute;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return AttributeLang
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set attribute
     *
     * @param \PrestaShopBundle\Entity\Attribute $attribute
     *
     * @return AttributeLang
     */
    public function setAttribute(\PrestaShopBundle\Entity\Attribute $attribute)
    {
        $this->attribute = $attribute;

        return $this;
    }

    /**
     * Get attribute
     *
     * @return \PrestaShopBundle\Entity\Attribute
     */
    public function getAttribute()
    {
        return $this->attribute;
    }

    /**
     * Set lang
     *
     * @param \PrestaShopBundle\Entity\Lang $lang
     *
     * @return AttributeLang
     */
    public function setLang(\PrestaShopBundle\Entity\Lang $lang)
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * Get lang
     *
     * @return \PrestaShopBundle\Entity\Lang
     */
    public function getLang()
    {
        return $this->lang;
    }
}
