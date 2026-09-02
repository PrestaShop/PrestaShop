<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form;

/**
 * Providers additional settings required for multi store form functionality.
 */
interface MultiStoreSettingsFormDataProviderInterface
{
    /**
     * Gets data which are used in form data providers.
     *
     * @return array - they key is the form type field name and the value is true or false. If true, then the multi shop
     *               checkbox value is selected and otherwise.
     */
    public function getData();
}
