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
 * RequiredField
 *
 * @ORM\Table(indexes={@ORM\Index(name="object_name", columns={"object_name"})})
 * @ORM\Entity
 */
class RequiredField
{
    /**
     * @var string
     *
     * @ORM\Column(name="object_name", type="string", length=32, nullable=false)
     */
    private $objectName;

    /**
     * @var string
     *
     * @ORM\Column(name="field_name", type="string", length=32, nullable=false)
     */
    private $fieldName;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_required_field", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRequiredField;



    /**
     * Set objectName
     *
     * @param string $objectName
     *
     * @return RequiredField
     */
    public function setObjectName($objectName)
    {
        $this->objectName = $objectName;

        return $this;
    }

    /**
     * Get objectName
     *
     * @return string
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * Set fieldName
     *
     * @param string $fieldName
     *
     * @return RequiredField
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Get fieldName
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Get idRequiredField
     *
     * @return integer
     */
    public function getIdRequiredField()
    {
        return $this->idRequiredField;
    }
}
