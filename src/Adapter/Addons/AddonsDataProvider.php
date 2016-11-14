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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
namespace PrestaShop\PrestaShop\Adapter\Addons;

use PrestaShop\PrestaShop\Adapter\Module\Module;
use PrestaShopBundle\Service\DataProvider\Admin\AddonsInterface;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;
use Symfony\Component\HttpFoundation\Request;
use Configuration;
use Context;
use Country;
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

    private $encryption;

    public $cacheDir;

    public function __construct(ApiClient $apiClient)
    {
        $this->marketplaceClient = $apiClient;
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
            return $this->unZip($temp_filename);
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
        if (!self::$is_addons_up) {
            return false;
        }

        // We merge the addons credentials
        if ($this->isAddonsAuthenticated()) {
            $params = array_merge($this->getAddonsCredentials(), $params);
        }

        $post_query_data = array(
            'version' => isset($params['version']) ? $params['version'] : _PS_VERSION_,
            'iso_lang' => Tools::strtolower(isset($params['iso_lang']) ? $params['iso_lang']
                        : Context::getContext()->language->iso_code),
            'iso_code' => Tools::strtolower(isset($params['iso_country']) ? $params['iso_country']
                        : Country::getIsoById(Configuration::get('PS_COUNTRY_DEFAULT'))),
            'shop_url' => isset($params['shop_url']) ? $params['shop_url'] : Tools::getShopDomain(),
            'mail' => isset($params['email']) ? $params['email'] : Configuration::get('PS_SHOP_EMAIL'),
            'format' => isset($params['format']) ? $params['format'] : 'xml',
        );
        if (isset($params['source'])) {
            $post_query_data['source'] = $params['source'];
        }

        $post_data = http_build_query($post_query_data);

        $protocols = array('https');
        $end_point = 'api.addons.prestashop.com';

        switch ($action) {
            case 'native' :
                return $this->marketplaceClient->getNativesModules();
                break;
            case 'service':
                return $this->marketplaceClient->getServices();
                break;
            case 'native_all':
                return $this->marketplaceClient->setIsoCode('all')
                    ->getNativesModules()
                ;
                break;
            case 'must-have':
                return $this->marketplaceClient->getMustHaveModules();
                break;
            case 'must-have-themes':
                $protocols[] = 'http';
                $post_data .= '&method=listing&action=must-have-themes';
                break;
            case 'customer':
                return $this->marketplaceClient->getCustomerModules($params['username_addons'], $params['password_addons']);
                break;
            case 'customer_themes':
                $post_data .= '&method=listing&action=customer-themes&username='.urlencode($params['username_addons'])
                    .'&password='.urlencode($params['password_addons']);
                break;
            case 'check_customer':
                $post_data .= '&method=check_customer&username='.urlencode($params['username_addons']).'&password='.urlencode($params['password_addons']);
                break;
            case 'check_module':
                $post_data .= '&method=check&module_name='.urlencode($params['module_name']).'&module_key='.urlencode($params['module_key']);
                break;
            case 'module_download':
                $post_data .= '&method=module&id_module='.urlencode($params['id_module']);
                if (isset($params['username_addons']) && isset($params['password_addons'])) {
                    $post_data .= '&username='.urlencode($params['username_addons']).'&password='.urlencode($params['password_addons']);
                } else {
                    $protocols[] = 'http';
                }
                break;
            case 'module':
                return $this->marketplaceClient->getModule($params['id_module']);
                break;
            case 'hosted_module':
                $post_data .= '&method=module&id_module='.urlencode((int) $params['id_module']).'&username='.urlencode($params['hosted_email'])
                    .'&password='.urlencode($params['password_addons'])
                    .'&shop_url='.urlencode(isset($params['shop_url']) ? $params['shop_url']
                                : Tools::getShopDomain())
                    .'&mail='.urlencode(isset($params['email']) ? $params['email']
                                : Configuration::get('PS_SHOP_EMAIL'));
                $protocols[] = 'https';
                break;
            case 'install-modules' :
                $protocols[] = 'http';
                $post_data .= '&method=listing&action=install-modules';
                $post_data .= defined('_PS_HOST_MODE_') ? '-od' : '';
                break;
            case 'categories':
                return $this->marketplaceClient->getCategories();
                break;
            default:
                return false;
        }

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'content' => $post_data,
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'timeout' => 5,
            ),
        ));

        foreach ($protocols as $protocol) {
            $content = Tools::file_get_contents($protocol.'://'.$end_point,
                    false, $context);
            if (!$content) {
                continue;
            }

            if ($post_query_data['format'] == 'json' && ctype_print($content)) {
                $json_result = json_decode($content);
                if ($json_result === false) {
                    throw new Exception('Cannot decode JSON from Addons');
                }

                if (!empty($json_result->errors)) {
                    throw new Exception('Error received from Addons: '.json_encode($json_result->errors));
                }

                return $json_result;
            } else {
                return $content; // Return raw result
            }
        }

        self::$is_addons_up = false;
        throw new Exception('Cannot execute request '.$action.' to Addons');
    }

    /**
     * @param $moduleId
     * @return Module
     */
    public function getModuleById($moduleId)
    {
        $moduleAttributes = $this->request('module', array('id_module' => $moduleId));

        $attributes = $this->moduleRepository->getModuleAttributes($moduleAttributes['name']);

        foreach ($attributes->all() as $name => $value) {
            if (!array_key_exists($name, $moduleAttributes)) {
                $moduleAttributes[$name] = $value;
            }
        }

        return new Module($moduleAttributes);
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

    protected function unZip($filename)
    {
        $zip = new \ZipArchive();
        $result = false;

        if ($zip->open($filename) === true) {
            try {
                $result = $zip->extractTo(_PS_MODULE_DIR_);
                $zip->close();
            } catch (Exception $exception) {
                throw new Exception('Cannot unzip the module', 0, $exception);
            }
        } else {
            throw new Exception('Cannot open the zip file');
        }

        return $result;
    }
}
