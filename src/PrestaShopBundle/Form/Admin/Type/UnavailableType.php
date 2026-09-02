<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Type;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This form type is useful during development phase to show part of a form that are not available yet.
 */
class UnavailableType extends TranslatorAwareType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'required' => false,
        ]);
    }
}
