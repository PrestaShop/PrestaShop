<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\ProductPreferences;

use PrestaShop\PrestaShop\Core\Cache\Clearer\CacheClearerInterface;
use PrestaShop\PrestaShop\Core\Form\Handler;

/**
 * Class manages the data manipulated using forms
 * in "Configure > Shop Parameters > Product Settings" page.
 */
class ProductPreferencesFormHandler extends Handler
{
    /**
     * @var CacheClearerInterface
     */
    private $cacheClearer;

    /**
     * {@inheritdoc}
     */
    public function save(array $data)
    {
        $errors = $this->formDataProvider->setData($data);

        if (empty($errors)) {
            $this->cacheClearer->clear();

            if (isset($data['stock_management']) && !$data['stock_management']) {
                $data['allow_ordering_oos'] = 1;
            }
        }

        return parent::save($data);
    }

    /**
     * Inject the cache clearer if needed.
     *
     * @param CacheClearerInterface $cacheClearer the Cache clearer
     */
    public function setCacheClearer(CacheClearerInterface $cacheClearer)
    {
        $this->cacheClearer = $cacheClearer;
    }
}
