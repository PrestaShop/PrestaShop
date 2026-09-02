<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module\SourceHandler;

interface SourceHandlerInterface
{
    /**
     * @param mixed $source
     *
     * @return bool
     */
    public function canHandle($source): bool;

    public function getModuleName($source): ?string;

    public function handle(string $source): void;
}
