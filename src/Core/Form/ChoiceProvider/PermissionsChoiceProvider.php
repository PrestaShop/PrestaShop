<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Domain\Webservice\ValueObject\Permission;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Provides webservice key permissions choices
 */
final class PermissionsChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        return [
            $this->translator->trans('All', [], 'Admin.Global') => 'all',
            $this->translator->trans('View (GET)', [], 'Admin.Advparameters.Feature') => Permission::VIEW,
            $this->translator->trans('Modify (PUT)', [], 'Admin.Advparameters.Feature') => Permission::MODIFY,
            $this->translator->trans('Add (POST)', [], 'Admin.Advparameters.Feature') => Permission::ADD,
            $this->translator->trans('Delete (DELETE)', [], 'Admin.Advparameters.Feature') => Permission::DELETE,
            $this->translator->trans('Fast view (HEAD)', [], 'Admin.Advparameters.Feature') => Permission::FAST_VIEW,
        ];
    }
}
