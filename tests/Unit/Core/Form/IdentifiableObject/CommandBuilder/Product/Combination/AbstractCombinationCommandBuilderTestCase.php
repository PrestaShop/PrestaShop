<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\Combination;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use Tests\Unit\Core\Form\IdentifiableObject\CommandBuilder\Product\AbstractMultiShopCommandsBuilderTestCase;

/**
 * Base class to test a combination command builder
 */
abstract class AbstractCombinationCommandBuilderTestCase extends AbstractMultiShopCommandsBuilderTestCase
{
    /**
     * @var CombinationId
     */
    private $combinationId;

    /**
     * @return CombinationId
     */
    protected function getCombinationId(): CombinationId
    {
        if (null === $this->combinationId) {
            $this->combinationId = new CombinationId(43);
        }

        return $this->combinationId;
    }
}
