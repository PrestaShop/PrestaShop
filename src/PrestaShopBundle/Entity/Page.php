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
 * Page
 *
 * @ORM\Table(indexes={@ORM\Index(name="id_page_type", columns={"id_page_type"}), @ORM\Index(name="id_object", columns={"id_object"})})
 * @ORM\Entity
 */
class Page
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_page_type", type="integer", nullable=false)
     */
    private $idPageType;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_object", type="integer", nullable=true)
     */
    private $idObject;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_page", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPage;



    /**
     * Set idPageType
     *
     * @param integer $idPageType
     *
     * @return Page
     */
    public function setIdPageType($idPageType)
    {
        $this->idPageType = $idPageType;

        return $this;
    }

    /**
     * Get idPageType
     *
     * @return integer
     */
    public function getIdPageType()
    {
        return $this->idPageType;
    }

    /**
     * Set idObject
     *
     * @param integer $idObject
     *
     * @return Page
     */
    public function setIdObject($idObject)
    {
        $this->idObject = $idObject;

        return $this;
    }

    /**
     * Get idObject
     *
     * @return integer
     */
    public function getIdObject()
    {
        return $this->idObject;
    }

    /**
     * Get idPage
     *
     * @return integer
     */
    public function getIdPage()
    {
        return $this->idPage;
    }
}
