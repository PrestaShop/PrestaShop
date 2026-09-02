<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Presenter\Store;

use Hook;
use Language;
use Link;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use Store;
use Symfony\Contracts\Translation\TranslatorInterface;

class StorePresenter
{
    /**
     * @var ImageRetriever
     */
    protected $imageRetriever;

    /**
     * @var Link
     */
    protected $link;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    public function __construct(
        Link $link,
        TranslatorInterface $translator
    ) {
        $this->link = $link;
        $this->imageRetriever = new ImageRetriever($link);
        $this->translator = $translator;
    }

    /**
     * @param array|Store $store Store object or an array
     * @param Language $language
     *
     * @return StoreLazyArray
     */
    public function present($store, $language)
    {
        // Convert to array if a Store object was passed
        if (is_object($store)) {
            $store = (array) $store;
        }

        // Normalize IDs
        if (empty($store['id_store'])) {
            $store['id_store'] = $store['id'];
        }
        if (empty($store['id'])) {
            $store['id'] = $store['id_store'];
        }

        $storeLazyArray = new StoreLazyArray(
            $store,
            $language,
            $this->imageRetriever,
            $this->translator
        );

        Hook::exec('actionPresentStore',
            ['presentedStore' => &$storeLazyArray]
        );

        return $storeLazyArray;
    }
}
