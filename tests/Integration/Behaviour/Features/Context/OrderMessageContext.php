<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Behaviour\Features\Context;

use OrderMessage;
use RuntimeException;

class OrderMessageContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @Then order message :reference :propertyName in default language should be :propertyValue
     */
    public function assertPropertyInDefaultLanguage(string $reference, string $propertyName, string $propertyValue)
    {
        /** @var OrderMessage $orderMessage */
        $orderMessage = SharedStorage::getStorage()->get($reference);

        $defaultLangId = CommonFeatureContext::getContainer()
            ->get('prestashop.adapter.legacy.configuration')
            ->get('PS_LANG_DEFAULT');

        $actualValue = $orderMessage->{$propertyName}[$defaultLangId];

        if ($actualValue !== $propertyValue) {
            throw new RuntimeException(sprintf('Order message "%d" property "%s" was expected to have value "%s", but has "%s" instead', $orderMessage->id, $propertyName, $propertyValue, $actualValue));
        }
    }
}
