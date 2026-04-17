<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use WebserviceRequest;

/**
 * Provides resources that can be accessed by webservice request
 *
 * @internal
 */
final class ResourcesChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $legacyResources = WebserviceRequest::getResources();
        $choices = [];

        foreach (array_keys($legacyResources) as $resource) {
            $choices[$resource] = $resource;
        }

        return $choices;
    }
}
