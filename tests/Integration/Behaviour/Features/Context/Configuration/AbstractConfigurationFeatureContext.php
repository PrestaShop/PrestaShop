<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context\Configuration;

use Behat\Behat\Context\Context as BehatContext;
use Configuration;
use Tests\Resources\DatabaseDump;
use Tools;

abstract class AbstractConfigurationFeatureContext implements BehatContext
{
    protected $configurationModified = false;

    protected function setConfiguration($index, $value)
    {
        $this->configurationModified = true;
        Configuration::updateGlobalValue($index, $value);
        Configuration::resetStaticCache();
        if ($index === 'PS_PRICE_ROUND_MODE') {
            Tools::$round_mode = null;
        }
    }

    /**
     * This hook can be used to reset changed configuration
     *
     * @AfterScenario
     */
    public function restoreConfigurationValues()
    {
        if ($this->configurationModified) {
            DatabaseDump::restoreTables(['configuration', 'configuration_lang']);
            Configuration::resetStaticCache();
            // Cached by Tools::ps_round()
            Tools::$round_mode = null;
        }
        $this->configurationModified = false;
    }
}
