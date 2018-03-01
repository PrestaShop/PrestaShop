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

namespace PrestaShop\PrestaShop\Adapter\Module\Tab;

use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShopBundle\Entity\Tab;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Tab as TabClass;

class ModuleTabUnregister
{
    /**
     * @var LangRepository
     */
    protected $langRepository;

    /**
     * @var TabRepository
     */
    protected $tabRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TabRepository $tabRepository, LangRepository $langRepository, LoggerInterface $logger, TranslatorInterface $translator)
    {
        $this->langRepository = $langRepository;
        $this->tabRepository = $tabRepository;
        $this->logger = $logger;
        $this->translator = $translator;
    }

    /**
     * Uninstall all module-defined tabs.
     *
     * This is done automatically as part of the module uninstallation.
     *
     * @return bool Returns true if the module tabs were successfully uninstalled, false if any of them failed to do so.
     */
    public function unregisterTabs(Module $module)
    {
        // We use the Tab repository to have only
        // installed tabs related to the module
        $tabs = $this->tabRepository->findByModule($module->get('name'));

        foreach ($tabs as $tab) {
            $this->unregisterTab($tab);
            $this->removeDuplicatedParent($tab);
        }
    }

    /**
     * Uninstalls a tab given its defined structure.
     *
     * @param Tab $tab The instance of entity tab.
     *
     */
    private function unregisterTab(Tab $tab)
    {
        // We need to use the legacy class because of the right management
        $tab_legacy = new TabClass($tab->getId());

        if (!$tab_legacy->delete()) {
            $this->logger->warning(
                $this->translator->trans(
                    'Failed to uninstall admin tab "%name%".',
                    array(
                        '%name%' => $tab->getClassName(),
                    ),
                    'Admin.Modules.Notification'));
        }
    }

    /**
     * When we add a level of children in the menu tabs, we created a dummy parent.
     * We must delete it when it has no more children than the original tab.
     *
     * @param Tab $tab
     */
    private function removeDuplicatedParent(Tab $tab)
    {
        $remainingChildren = $this->tabRepository->findByParentId($tab->getIdParent());
        if (count($remainingChildren) > 1) {
            return;
        }

        $parent = $this->tabRepository->find($tab->getIdParent());
        $child = end($remainingChildren);

        // We know we have a tab to delete if the parent name is the remaining child name+_MTR
        if ($parent->getClassName() === $child->getClassName().ModuleTabRegister::suffix) {
            $legacyTabParent = new TabClass($parent->getId());
            // Setting a wrong id_parent will prevent the children to move
            $legacyTabParent->id_parent = -1;
            $legacyTabParent->delete();

            $legacyTab = new TabClass($child->getId());
            $legacyTab->id_parent = $parent->getIdParent();
            $legacyTab->save();
            // Updating the id_parent will override the position, that's why we save 2 times
            $legacyTab->position = $parent->getPosition();
            $legacyTab->save();
        }
    }
}
