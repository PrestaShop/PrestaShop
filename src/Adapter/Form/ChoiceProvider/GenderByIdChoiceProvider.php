<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Gender;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class GenderByIdChoiceProvider provides gender choices with label as title and value as gender id.
 */
final class GenderByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $genders = Gender::getGenders();
        $choices = [];

        /** @var Gender $gender */
        foreach ($genders as $gender) {
            $choices[$gender->name] = $gender->id;
        }

        return $choices;
    }
}
