<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler;

use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler\FormDataHandlerInterface;

/**
 * Defines interface for form handler factories.
 */
interface FormHandlerFactoryInterface
{
    /**
     * Creates new form handler with given data handler.
     *
     * @param FormDataHandlerInterface $dataHandler
     *
     * @return FormHandlerInterface
     */
    public function create(FormDataHandlerInterface $dataHandler);
}
