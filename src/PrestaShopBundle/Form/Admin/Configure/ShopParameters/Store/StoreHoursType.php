<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Store;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Renders 7 text inputs (Monday–Sunday) for opening/closing hours.
 * Each value is expected in "HH:MM | HH:MM" format (e.g. "09:00 | 18:00").
 */
class StoreHoursType extends AbstractType
{
    private const DAY_LABELS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach (self::DAY_LABELS as $index => $label) {
            $builder->add((string) $index, TextType::class, [
                'label' => $label,
                'required' => false,
                'attr' => ['placeholder' => 'e.g. 09:00 | 18:00'],
            ]);
        }
    }
}
