<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Risk;

/**
 * Class RiskByIdChoiceProvider
 *
 * @internal
 */
final class RiskByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $risks = Risk::getRisks();
        $choices = [];

        /** @var Risk $risk */
        foreach ($risks as $risk) {
            $choices[$risk->name] = (int) $risk->id;
        }

        return $choices;
    }
}
