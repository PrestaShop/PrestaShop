<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Row\Type;

use PrestaShop\PrestaShop\Core\Grid\Action\Row\AbstractRowAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Row\AccessibilityChecker\AccessibilityCheckerInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines row action as form submit.
 */
final class SubmitRowAction extends AbstractRowAction
{
    public const MESSAGE_TYPE_STATIC = 'static'; // Static confirmation message type is standard confirmation message type
    public const MESSAGE_TYPE_DYNAMIC = 'dynamic'; // Dynamic confirmation message type enables dynamic confirmation message

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'submit';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired([
                'route',
                'route_param_name',
                'route_param_field',
            ])
            ->setDefaults([
                'method' => 'POST',
                'confirm_message' => '',
                'accessibility_checker' => null,
                'confirm_message_type' => self::MESSAGE_TYPE_STATIC,
                'dynamic_message_field' => '',
                'extra_route_params' => [],
                'modal_options' => null,
            ])
            ->setAllowedTypes('route', 'string')
            ->setAllowedTypes('route_param_name', 'string')
            ->setAllowedTypes('route_param_field', 'string')
            ->setAllowedTypes('extra_route_params', 'array')
            ->setAllowedTypes('method', 'string')
            ->setAllowedTypes('confirm_message', 'string')
            ->setAllowedTypes('accessibility_checker', [AccessibilityCheckerInterface::class, 'callable', 'null'])
            ->setAllowedTypes('dynamic_message_field', 'string')
            ->setAllowedTypes('confirm_message_type', 'string')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function isApplicable(array $record)
    {
        $accessibilityChecker = $this->getOptions()['accessibility_checker'];

        if ($accessibilityChecker instanceof AccessibilityCheckerInterface) {
            return $accessibilityChecker->isGranted($record);
        }

        if (is_callable($accessibilityChecker)) {
            return call_user_func($accessibilityChecker, $record);
        }

        return parent::isApplicable($record);
    }
}
