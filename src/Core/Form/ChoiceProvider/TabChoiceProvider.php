<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class TabChoiceProvider provides Tab choices with name values.
 *
 * This class is used for choosing Default page when creating employee
 */
final class TabChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var array
     */
    private $tabs;

    /**
     * @param array $tabs
     */
    public function __construct(array $tabs)
    {
        $this->tabs = $tabs;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $choices = [];

        foreach ($this->tabs as $tab) {
            if (!empty($tab['children'])) {
                $choices[$tab['name']] = [];

                foreach ($tab['children'] as $childTab) {
                    if ($childTab['name']) {
                        $choices[$tab['name']][$childTab['name']] = $childTab['id_tab'];
                    }
                }
            } else {
                $choices[$tab['name']] = $tab['id_tab'];
            }
        }

        return $choices;
    }
}
