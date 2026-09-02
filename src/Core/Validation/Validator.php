<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Validation;

use PrestaShop\PrestaShop\Adapter\Validate;

/**
 * Class Validator is responsible for validating data.
 */
final class Validator implements ValidatorInterface
{
    /**
     * @var Validate
     */
    private $validate;

    /**
     * @param Validate $validate
     */
    public function __construct(Validate $validate)
    {
        $this->validate = $validate;
    }

    /**
     * {@inheritdoc}
     */
    public function isCleanHtml($html, array $options = [])
    {
        $defaultOptions = [
            'allow_iframe' => false,
        ];
        $options = array_merge($defaultOptions, $options);

        return $this->validate->isCleanHtml($html, $options['allow_iframe']);
    }

    /**
     * {@inheritdoc}
     */
    public function isModuleName($name)
    {
        return $this->validate->isModuleName($name);
    }
}
