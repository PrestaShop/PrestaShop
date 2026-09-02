<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search;

interface SearchPanelInterface
{
    public function getTitle(): string;

    public function getButtonLabel(): string;

    public function getLink(): string;

    public function isExternalLink(): bool;
}
