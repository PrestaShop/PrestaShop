<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Builder;

use Symfony\Component\Form\FormInterface;

/**
 * Defines contract for identifiable object form factories.
 */
interface FormBuilderInterface
{
    /**
     * Create new form.
     *
     * @param array $data
     * @param array $options
     *
     * @return FormInterface
     */
    public function getForm(array $data = [], array $options = []);

    /**
     * Create new form for given object.
     *
     * @param int|string $id
     * @param array $data
     * @param array $options
     *
     * @return FormInterface
     */
    public function getFormFor($id, array $data = [], array $options = []);
}
