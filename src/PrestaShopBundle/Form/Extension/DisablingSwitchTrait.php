<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Extension;

use Closure;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormInterface;
use TypeError;

/**
 * This trait is used by the DisablingSwitchExtension and the AddDisablingSwitchListener because they both need
 * to detect if a form should be disabled and the need to detect it the same way.
 */
trait DisablingSwitchTrait
{
    protected function shouldFormBeDisabled(FormInterface $form, $data): bool
    {
        $disabledValue = $form->getConfig()->getOption(DisablingSwitchExtension::DISABLED_VALUE_OPTION);
        if (is_callable($disabledValue)) {
            try {
                $shouldBeDisabled = $disabledValue($data, $form);
            } catch (TypeError $typeError) {
                throw new InvalidConfigurationException(
                    'The callable provided for disabled_value option seems invalid, its prototype should be compatible with function($data, FormInterface $form): bool And $data is usually nullable',
                    0,
                    $typeError
                );
            }
        } else {
            if (null === $disabledValue) {
                $disabledValue = $form->getConfig()->getOption('default_empty_data');
            }
            if (null === $disabledValue) {
                $emptyData = $form->getConfig()->getOption('empty_data');
                $disabledValue = $emptyData instanceof Closure ? $emptyData($form) : $emptyData;
            }

            $shouldBeDisabled = $disabledValue === $data;
        }

        return $shouldBeDisabled;
    }
}
