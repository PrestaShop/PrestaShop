<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;

/**
 * Interface ImportFormHandlerInterface describes an import form handler.
 */
interface ImportFormHandlerInterface
{
    /**
     * Get the import form.
     *
     * @param ImportConfigInterface $importConfig
     *
     * @return mixed
     */
    public function getForm(ImportConfigInterface $importConfig);

    /**
     * Save the form's data.
     *
     * @param array $data
     *
     * @return array of errors
     */
    public function save(array $data);
}
