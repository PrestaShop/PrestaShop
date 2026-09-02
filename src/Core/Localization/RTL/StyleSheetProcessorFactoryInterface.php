<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Localization\RTL;

/**
 * Interface StyleSheetProcessorFactoryInterface creates RTL stylesheet processor.
 */
interface StyleSheetProcessorFactoryInterface
{
    /**
     * @return Processor
     */
    public function create();
}
