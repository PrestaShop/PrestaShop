<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

class TagFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @When I define an uncreated tag :tagReference
     *
     * @param string $tagReference
     */
    public function defineUnCreatedTag(string $tagReference): void
    {
        SharedStorage::getStorage()->set($tagReference, 1234567);
    }
}
