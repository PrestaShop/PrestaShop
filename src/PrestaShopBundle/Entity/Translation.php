<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use PrestaShopBundle\Translation\Constraints\PassVsprintf;

/**
 * Translation.
 *
 * @ORM\Table(
 *     indexes={@ORM\Index(name="key", columns={"domain"})},
 * )
 *
 * @ORM\Entity(repositoryClass="PrestaShopBundle\Entity\Repository\TranslationRepository")
 *
 * @PassVsprintf
 */
class Translation
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id_translation", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity="Lang", inversedBy="translations")
     *
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false)
     */
    private Lang $lang;

    /**
     * @ORM\Column(name="`key`", type="text", length=8000)
     */
    private string $key;

    /**
     * @ORM\Column(name="translation", type="text", length=65500)
     */
    private string $translation;

    /**
     * @ORM\Column(name="domain", type="string", length=80)
     */
    private string $domain;

    /**
     * @ORM\Column(name="theme", type="string", length=32, nullable=true)
     */
    private ?string $theme = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getTranslation(): string
    {
        return $this->translation;
    }

    public function getLang(): Lang
    {
        return $this->lang;
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    public function setTranslation(string $translation): static
    {
        $this->translation = $translation;

        return $this;
    }

    public function setLang(Lang $lang): static
    {
        $this->lang = $lang;

        return $this;
    }

    public function setDomain(string $domain): static
    {
        $this->domain = $domain;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): static
    {
        $this->theme = $theme;

        return $this;
    }
}
