<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Action\Type;

use PrestaShop\PrestaShop\Core\Grid\Action\AbstractGridAction;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class SubmitGridAction represents grid action that can be submitted.
 */
final class SubmitGridAction extends AbstractGridAction
{
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
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'submit_route',
            ])
            ->setDefaults([
                'submit_method' => 'POST',
                'confirm_message' => null,
            ])
            ->setAllowedTypes('submit_route', 'string')
            ->setAllowedTypes('confirm_message', ['null', 'string'])
            ->setAllowedValues('submit_method', ['POST', 'GET']);
    }
}
