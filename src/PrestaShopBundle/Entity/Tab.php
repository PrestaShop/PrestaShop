<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tab
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\TabRepository")
 */
class Tab
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id_tab", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_parent", type="integer")
     */
    private $idParent;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="module", type="string", length=64, nullable=true)
     */
    private $module;

    /**
     * @var string
     *
     * @ORM\Column(name="class_name", type="string", length=64, nullable=true)
     */
    private $className;

    /**
     * @var boolean
     *
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var boolean
     *
     * @ORM\Column(name="hide_host_mode", type="boolean")
     */
    private $hideHostMode;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=32)
     */
    private $icon;

    /**
     * @ORM\OneToMany(targetEntity="PrestaShopBundle\Entity\TabLang", mappedBy="tab")
     */
    private $tabLangs;


    public function getId()
    {
        return $this->id;
    }

    public function getIdParent()
    {
        return $this->idParent;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function getHideHostMode()
    {
        return $this->hideHostMode;
    }

    public function getIcon()
    {
        return $this->icon;
    }

    public function getTabLangs()
    {
        return $this->tabLangs;
    }
}
