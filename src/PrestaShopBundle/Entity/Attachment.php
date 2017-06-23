<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Attachment
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Attachment
{
    /**
     * @var string
     *
     * @ORM\Column(name="file", type="string", length=40, nullable=false)
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="file_name", type="string", length=128, nullable=false)
     */
    private $fileName;

    /**
     * @var integer
     *
     * @ORM\Column(name="file_size", type="bigint", nullable=false)
     */
    private $fileSize = '0';

    /**
     * @var string
     *
     * @ORM\Column(name="mime", type="string", length=128, nullable=false)
     */
    private $mime;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_attachment", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idAttachment;



    /**
     * Set file
     *
     * @param string $file
     *
     * @return Attachment
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set fileName
     *
     * @param string $fileName
     *
     * @return Attachment
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
     * Set fileSize
     *
     * @param integer $fileSize
     *
     * @return Attachment
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize
     *
     * @return integer
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }

    /**
     * Set mime
     *
     * @param string $mime
     *
     * @return Attachment
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * Get mime
     *
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * Get idAttachment
     *
     * @return integer
     */
    public function getIdAttachment()
    {
        return $this->idAttachment;
    }
}
