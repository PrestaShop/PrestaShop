<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;

/**
 * Interface ImportFormDataProviderInterface describes a data provider for import forms.
 */
interface ImportFormDataProviderInterface
{
    /**
     * Get form's data.
     *
     * @param ImportConfigInterface $importConfig
     *
     * @return array
     */
    public function getData(ImportConfigInterface $importConfig);

    /**
     * Save the form's data.
     *
     * @param array $data
     *
     * @return array of errors, if occurred
     */
    public function setData(array $data);
}
