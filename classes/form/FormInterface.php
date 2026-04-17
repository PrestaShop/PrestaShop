<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
use PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableInterface;

interface FormInterface extends RenderableInterface
{
    public function setAction($action);

    public function fillWith(array $params = []);

    public function submit();

    public function getErrors();

    public function hasErrors();

    public function render(array $extraVariables = []);

    public function setTemplate($template);
}
