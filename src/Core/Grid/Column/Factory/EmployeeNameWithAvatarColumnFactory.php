<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Grid\Column\Factory;

use PrestaShop\PrestaShop\Core\Grid\Column\Column;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class EmployeeNameWithAvatarColumnFactory is responsible for creating column which renders employee name with avatar
 */
final class EmployeeNameWithAvatarColumnFactory implements ColumnFactoryInterface
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     * @param EngineInterface $templating
     */
    public function __construct(
        TranslatorInterface $translator,
        EngineInterface $templating
    ) {
        $this->templating = $templating;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $templating = $this->templating;
        $displayEmployee = function ($row) use ($templating) {
            return $templating->render('@PrestaShop/Admin/Configure/AdvancedParameters/LogsPage/Blocks/employee_block.html.twig', [
                'row' => $row,
            ]);
        };

        return Column::fromArray([
            'id' => 'employee',
            'name' => $this->translator->trans('Employee', [], 'Admin.Global'),
            'filter_form_type' => TextType::class,
            'modifier' => $displayEmployee,
            'raw_content' => true,
        ]);
    }
}
