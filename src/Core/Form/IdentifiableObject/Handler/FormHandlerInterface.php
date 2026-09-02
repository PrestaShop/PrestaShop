<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler;

use Symfony\Component\Form\FormInterface;

/**
 * Defines interface for handling identifiable object's form.
 */
interface FormHandlerInterface
{
    /**
     * Handles form by creating new object.
     *
     * @param FormInterface $form
     *
     * @return FormHandlerResultInterface
     */
    public function handle(FormInterface $form);

    /**
     * Handles form for given object.
     *
     * @param int|string $id
     * @param FormInterface $form
     *
     * @return FormHandlerResultInterface
     */
    public function handleFor($id, FormInterface $form);
}
