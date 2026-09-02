<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

use Configuration;
use Risk;
use RuntimeException;

class RiskFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Given risk :reference in default language named :name exists
     */
    public function checkRiskWithNameExists($reference, $name)
    {
        $risks = Risk::getRisks((int) Configuration::get('PS_LANG_DEFAULT'));

        $searchedRisk = null;
        /** @var Risk $risk */
        foreach ($risks as $risk) {
            if ($risk->name === $name) {
                $searchedRisk = $risk;
                break;
            }
        }

        if (!$searchedRisk) {
            throw new RuntimeException(sprintf('Cannot find risk with name %s', $name));
        }

        SharedStorage::getStorage()->set($reference, $searchedRisk->id);
    }
}
