<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Module\Tab;

use Exception;
use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShopBundle\Entity\Repository\LangRepository;
use PrestaShopBundle\Entity\Repository\TabRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Translation\TranslatorInterface;
use TabCore as Tab;

class ModuleTabRegister
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
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var Symfony\Component\Translation\TranslatorInterface
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
     * Install all module-defined tabs.
     *
     * This is done automatically as part of the module installation.
     * @param Module $module
     * 
     */
    public function registerTabs(Module $module)
    {
        foreach ($module->getInstance()->getTabs() as $tab) {
            try {
                $this->registerTab(new ParameterBag($module, $tab));
            } catch (Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
    }
    
    protected function checkIsValid(Module $module, ParameterBag $data)
    {
        $className = $data->get('class_name', null);
        if (null === $className) {
            throw new Exception('Missing class name of tab');
        }
        // Check controller exists
        if (!file_exists(_PS_ROOT_DIR_.
                DIRECTORY_SEPARATOR.basename(_PS_MODULE_DIR_).
                DIRECTORY_SEPARATOR.$module->get('name').
                DIRECTORY_SEPARATOR.'controllers'.
                DIRECTORY_SEPARATOR.'admin'.
                DIRECTORY_SEPARATOR.$className.'Controller.php')) {
            throw new Exception(sprintf('Class "%sController" not found in controllers/admin', $className));
        }
    }
    
    /**
     * Install a tab according to its defined structure
     *
     * @param Module $module
     * @param ParameterBag $data The structure of the tab.
     *
     * @throws Exception in case of error from validation or save
     */
    protected function registerTab(Module $module, ParameterBag $data)
    {
        $this->checkIsValid($module, $data);
        
        // Legacy Tab, to be replaced with Doctrine entity when right management
        // won't be directly linked to the tab creation
        // @ToDo
        $tab = new Tab();
        $tab->active = $data->getBoolean('active', false);
        $tab->class_name = $data->get('class_name');
        $tab->module = $module->get('name');
        $tab->name = $data->get('name', $tab->class_name);

        // Handle hidden or root position
        $parentClassName = $data->get('ParentClassName', null);
        if (true === $data->getBoolean('hidden', false)) {
            $tab->id_parent = -1;
        } elseif (!empty($parentClassName)) {
            $tab->id_parent = $this->tabRepository->findOneByClassName($parentClassName);
        } else {
            $tab->id_parent = 0;
        }
        
        if (!$tab->save()) {
            throw new Exception(
                $this->translator->trans(
                    'Failed to install admin tab "%name%".',
                    array(
                        '%name%' => $tab->name,
                    ),
                    'Admin.Modules.Notification'));
        }
    }
}
