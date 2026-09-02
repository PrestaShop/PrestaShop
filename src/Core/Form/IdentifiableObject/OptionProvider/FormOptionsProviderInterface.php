<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\OptionProvider;

/**
 * Interface for services that provide options for identifiable object forms.
 */
interface FormOptionsProviderInterface
{
    /**
     * Get form options for given object with given id.
     *
     * @param int $id
     * @param array $data
     *
     * @return array
     */
    public function getOptions(int $id, array $data): array;

    /**
     * Get default form options.
     *
     * @param array $data
     *
     * @return array
     */
    public function getDefaultOptions(array $data): array;
}
