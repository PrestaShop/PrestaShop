<?php

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mail
 *
 * @ORM\Table(indexes={@ORM\Index(name="recipient", columns={"recipient"})})
 * @ORM\Entity
 */
class Mail
{
    /**
     * @var string
     *
     * @ORM\Column(name="recipient", type="string", length=126, nullable=false)
     */
    private $recipient;

    /**
     * @var string
     *
     * @ORM\Column(name="template", type="string", length=62, nullable=false)
     */
    private $template;

    /**
     * @var string
     *
     * @ORM\Column(name="subject", type="string", length=254, nullable=false)
     */
    private $subject;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_lang", type="integer", nullable=false)
     */
    private $idLang;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime", nullable=false)
     */
    private $dateAdd = 'CURRENT_TIMESTAMP';

    /**
     * @var integer
     *
     * @ORM\Column(name="id_mail", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMail;



    /**
     * Set recipient
     *
     * @param string $recipient
     *
     * @return Mail
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get recipient
     *
     * @return string
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set template
     *
     * @param string $template
     *
     * @return Mail
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get template
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Mail
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set idLang
     *
     * @param integer $idLang
     *
     * @return Mail
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
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Mail
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
     * Get idMail
     *
     * @return integer
     */
    public function getIdMail()
    {
        return $this->idMail;
    }
}
