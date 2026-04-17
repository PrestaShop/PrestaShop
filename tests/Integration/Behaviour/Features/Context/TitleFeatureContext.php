<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context;

class TitleFeatureContext extends AbstractPrestaShopFeatureContext
{
    /**
     * @When I define an uncreated title :titleReference
     *
     * @param string $titleReference
     */
    public function defineUnCreatedTitle(string $titleReference): void
    {
        SharedStorage::getStorage()->set($titleReference, 1234567);
    }
}
