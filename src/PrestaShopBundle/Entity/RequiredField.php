<?php

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
