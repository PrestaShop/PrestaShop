<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
