<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Api;

use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractApi
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFormattedTranslations()
    {
        $all = [];

        foreach ($this->getTranslations() as $key => $translation) {
            $all[] = [
                'translation_id' => $key,
                'name' => $translation,
            ];
        }

        return $all;
    }

    abstract public function getTranslations();
}
