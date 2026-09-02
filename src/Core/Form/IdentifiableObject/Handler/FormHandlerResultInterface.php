<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler;

/**
 * Defines interface for form handler result DTO
 */
interface FormHandlerResultInterface
{
    /**
     * Check if form is valid and does not contains any errors
     *
     * @return bool
     */
    public function isValid();

    /**
     * Check if form was actually submitted
     *
     * @return bool
     */
    public function isSubmitted();

    /**
     * Get identifiable object id
     *
     * @return int|mixed|null ID of identifiable object or null if it does not exist
     */
    public function getIdentifiableObjectId(): mixed;
}
