<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search;

/**
 * Utility class to extract information from modern controller FQCN.
 */
final class ControllerAction
{
    /**
     * Retrieve the Controller's action and name from a FQCN notation of Symfony controller.
     * This function expects a string like MyNamespace\Foo\FooController::bazAction.
     *
     * @param string $controller
     *
     * @return array
     */
    public static function fromString($controller)
    {
        return [
            self::getControllerName($controller),
            self::getActionName($controller),
        ];
    }

    /**
     * Get current controller name.
     *
     * @param string $controller the full controller name
     *
     * @return string
     */
    private static function getControllerName($controller)
    {
        preg_match('~(\w+)Controller(?:::(?:\w+)Action)?$~', $controller, $matches);

        return !empty($matches) ? strtolower($matches[1]) : 'N/A';
    }

    /**
     * Get current action name.
     *
     * @param string $controller the full controller name
     *
     * @return string
     */
    private static function getActionName($controller)
    {
        preg_match('~::(\w+)Action$~', $controller, $matches);

        return !empty($matches) ? strtolower($matches[1]) : 'N/A';
    }
}
