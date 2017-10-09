<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Addons;

use PrestaShop\PrestaShop\Adapter\Module\ModuleZipManager;
use PrestaShopBundle\Service\DataProvider\Admin\AddonsInterface;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Symfony\Component\HttpFoundation\Request;
use Configuration;
use Exception;
use Tools;
use PhpEncryption;

/**
 * Data provider for new Architecture, about Addons.
 *
 * This class will provide data from Addons API
 */
class AddonsDataProvider implements AddonsInterface
{
    protected static $is_addons_up = true;

    private $marketplaceClient;

    private $zipManager;

    private $encryption;

    public $cacheDir;

    public function __construct(ApiClient $apiClient, ModuleZipManager $zipManager)
    {
        $this->marketplaceClient = $apiClient;
        $this->zipManager = $zipManager;
        $this->encryption = new PhpEncryption(_NEW_COOKIE_KEY_);
    }

    public function downloadModule($module_id)
    {
        $params = array(
            'id_module' => $module_id,
            'format' => 'json',
        );

        // Module downloading
        try {
            $module_data = $this->request('module_download', $params);
        } catch (Exception $e) {
            if (!$this->isAddonsAuthenticated()) {
                throw new Exception('Error sent by Addons. You may need to be logged.', 0, $e);
            } else {
                throw new Exception('Error sent by Addons. You may be not allowed to download this module.', 0, $e);
            }
        }

        $temp_filename = tempnam($this->cacheDir, 'mod');
        if (file_put_contents($temp_filename, $module_data) !== false) {
            $this->zipManager->storeInModulesFolder($temp_filename);
            return true;
        } else {
            throw new Exception('Cannot store module content in temporary folder !');
        }
    }

    /** Does this function should be in a User related class ? **/
    public function isAddonsAuthenticated()
    {
        $request = Request::createFromGlobals();

        return $request->cookies->get('username_addons', false)
            && $request->cookies->get('password_addons', false);
    }

    /**
     * {@inheritdoc}
     */
    public function request($action, $params = array())
    {
        if (!$this->isAddonsUp()) {
            throw new Exception('Previous call failed and disabled client.');
        }

        // We merge the addons credentials
        if ($this->isAddonsAuthenticated()) {
            $params = array_merge($this->getAddonsCredentials(), $params);
        }

        $this->marketplaceClient->reset();

        try {
            switch ($action) {
                case 'native':
                    return $this->marketplaceClient->getNativesModules();
                case 'service':
                    return $this->marketplaceClient->getServices();
                case 'native_all':
                    return $this->marketplaceClient->setIsoCode('all')
                        ->getNativesModules();
                case 'must-have':
                    return $this->marketplaceClient->getMustHaveModules();
                case 'customer':
                    return $this->marketplaceClient->getCustomerModules($params['username_addons'], $params['password_addons']);
                case 'customer_themes':
                    return $this->marketplaceClient
                        ->setUserMail($params['username_addons'])
                        ->setPassword($params['password_addons'])
                        ->getCustomerThemes();
                case 'check_customer':
                    return $this->marketplaceClient
                        ->setUserMail($params['username_addons'])
                        ->setPassword($params['password_addons'])
                        ->getCheckCustomer();
                case 'check_module':
                    return $this->marketplaceClient
                        ->setUserMail($params['username_addons'])
                        ->setPassword($params['password_addons'])
                        ->setModuleName($params['module_name'])
                        ->setModuleKey($params['module_key'])
                        ->getCheckModule();
                case 'module_download':
                    if ($this->isAddonsAuthenticated()) {
                        return $this->marketplaceClient
                            ->setUserMail($params['username_addons'])
                            ->setPassword($params['password_addons'])
                            ->getModuleZip($params['id_module']);
                    }
                    return $this->marketplaceClient->getModuleZip($params['id_module']);
                case 'module':
                    return $this->marketplaceClient->getModule($params['id_module']);
                case 'install-modules':
                    return $this->marketplaceClient->getPreInstalledModules();
                case 'categories':
                    return $this->marketplaceClient->getCategories();
            }
        } catch (Exception $e) {
            self::$is_addons_up = false;
            throw $e;
        }
    }

    protected function getAddonsCredentials()
    {
        $request = Request::createFromGlobals();
        $username = $this->encryption->decrypt($request->cookies->get('username_addons'));
        $password = $this->encryption->decrypt($request->cookies->get('password_addons'));

        return array(
           'username_addons' => $username,
           'password_addons' => $password,
        );
    }

    /** Does this function should be in a User related class ? **/
    public function getAddonsEmail()
    {
        $request = Request::createFromGlobals();
        $username = $this->encryption->decrypt($request->cookies->get('username_addons'));

        return array(
            'username_addons' => $username,
        );
    }

    /**
     * Check if a request has already failed.
     *
     * @return bool
     */
    public function isAddonsUp()
    {
        return self::$is_addons_up;
    }
}
