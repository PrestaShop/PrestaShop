<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Dashboard;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Date range selector of the dashboard page (per-employee stats range).
 *
 * Submitted with POST so the admin security token is preserved (a GET form would drop
 * it from the query string). Kept intentionally simple; it can be extended by modules
 * through the form-building hooks like any other admin form.
 */
class DashboardDateRangeType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_from', DateType::class, [
                'label' => $this->trans('From', 'Admin.Global'),
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => true,
            ])
            ->add('date_to', DateType::class, [
                'label' => $this->trans('To', 'Admin.Global'),
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => true,
            ]);
    }
}
