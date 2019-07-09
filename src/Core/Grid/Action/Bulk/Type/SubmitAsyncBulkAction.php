<?php

namespace PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type;

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\AbstractBulkAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * initialises async process of submitting data to server - this helps to divide request to server. Useful for large
 * operations.
 */
final class SubmitAsyncBulkAction extends AbstractBulkAction
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'submit_async';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'submit_route',
            ])
            ->setDefaults([
                'confirm_message' => null,
                'submit_method' => 'POST',
                'route_params' => [],
                'step' => 1,
            ])
            ->setAllowedTypes('submit_route', 'string')
            ->setAllowedTypes('confirm_message', ['string', 'null'])
            ->setAllowedValues('submit_method', ['POST', 'GET', 'DELETE', 'PUT'])
            ->setAllowedTypes('route_params', 'array')
            ->setAllowedTypes('step', 'int')
        ;

        $resolver->setAllowedValues('step', static function ($value) {
            return $value >= 1;
        });
    }
}
