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
 * Meta
 *
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="page", columns={"page"})})
 * @ORM\Entity
 */
class Meta
{
    /**
     * @var string
     *
     * @ORM\Column(name="page", type="string", length=64, nullable=false)
     */
    private $page;

    /**
     * @var boolean
     *
     * @ORM\Column(name="configurable", type="boolean", nullable=false, options={"default":1})
     */
    private $configurable = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_meta", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMeta;



    /**
     * Set page
     *
     * @param string $page
     *
     * @return Meta
     */
    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return string
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set configurable
     *
     * @param boolean $configurable
     *
     * @return Meta
     */
    public function setConfigurable($configurable)
    {
        $this->configurable = $configurable;

        return $this;
    }

    /**
     * Get configurable
     *
     * @return boolean
     */
    public function getConfigurable()
    {
        return $this->configurable;
    }

    /**
     * Get idMeta
     *
     * @return integer
     */
    public function getIdMeta()
    {
        return $this->idMeta;
    }
}
