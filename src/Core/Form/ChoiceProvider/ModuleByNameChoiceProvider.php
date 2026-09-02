<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Module\ModuleCollection;
use PrestaShop\PrestaShop\Core\Module\ModuleInterface;

/**
 * Class ModuleByNameChoiceProvider provides module choices with name values.
 */
final class ModuleByNameChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ModuleCollection collection of installed modules
     */
    private $installedModules;

    /**
     * @param ModuleCollection $installedModules
     */
    public function __construct(ModuleCollection $installedModules)
    {
        $this->installedModules = $installedModules;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $moduleChoices = [];

        /** @var ModuleInterface $module */
        foreach ($this->installedModules as $module) {
            $moduleChoices[$module->get('displayName')] = $module->get('name');
        }

        // Order modules alphabetically
        ksort($moduleChoices);

        return $moduleChoices;
    }
}
