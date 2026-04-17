<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Foundation\Templating;

interface RenderableInterface
{
    public function setTemplate($templatePath);

    public function getTemplate();

    public function render(array $extraParams = []);
}
