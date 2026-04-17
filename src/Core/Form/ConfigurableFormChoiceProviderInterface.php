<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form;

/**
 * Interface for services that provide configurable form choices (e.g. States choices depending on country).
 */
interface ConfigurableFormChoiceProviderInterface
{
    /**
     * @param array $options
     *
     * @return array
     */
    public function getChoices(array $options);
}
