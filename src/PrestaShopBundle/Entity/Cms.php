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
 * Cms
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Cms
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id_cms_category", type="integer", nullable=false)
     */
    private $idCmsCategory;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false, options={"default":0})
     */
    private $position = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean", nullable=false, options={"default":0})
     */
    private $active = '0';

    /**
     * @var boolean
     *
     * @ORM\Column(name="indexation", type="boolean", nullable=false, options={"default":1})
     */
    private $indexation = '1';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_cms", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCms;



    /**
     * Set idCmsCategory
     *
     * @param integer $idCmsCategory
     *
     * @return Cms
     */
    public function setIdCmsCategory($idCmsCategory)
    {
        $this->idCmsCategory = $idCmsCategory;

        return $this;
    }

    /**
     * Get idCmsCategory
     *
     * @return integer
     */
    public function getIdCmsCategory()
    {
        return $this->idCmsCategory;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return Cms
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set active
     *
     * @param boolean $active
     *
     * @return Cms
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
     * Set indexation
     *
     * @param boolean $indexation
     *
     * @return Cms
     */
    public function setIndexation($indexation)
    {
        $this->indexation = $indexation;

        return $this;
    }

    /**
     * Get indexation
     *
     * @return boolean
     */
    public function getIndexation()
    {
        return $this->indexation;
    }

    /**
     * Get idCms
     *
     * @return integer
     */
    public function getIdCms()
    {
        return $this->idCms;
    }
}
