<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait TranslatorAwareTrait is used for services that depends on translator.
 */
trait TranslatorAwareTrait
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Set translator instance.
     *
     * @param TranslatorInterface $translator
     */
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Shortcut method to translate text.
     *
     * @param string $id
     * @param array $options
     * @param string $domain
     *
     * @return string
     */
    protected function trans($id, array $options, $domain)
    {
        return $this->translator->trans($id, $options, $domain);
    }
}
