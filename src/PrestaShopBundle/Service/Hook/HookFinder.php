<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Service\Hook;

use PrestaShop\PrestaShop\Adapter\HookManager;

/**
 * This class declares the functions needed to get structured data
 * from the modules, by asking them to follow a specific class.
 */
class HookFinder
{
    /**
     * In order to not return wrong things to the caller, we ask for an
     * instance of specific classes.
     * Please note it must implement the function toArray().
     *
     * @var string
     */
    protected $expectedInstanceClasses = array();

    /**
     * Because we cannot send the same parameters between two different finders,
     * we need a attribute. It will be sent to the Hook::exec() function.
     *
     * @var array
     */
    protected $params = array();

    /**
     * The hook to call.
     *
     * @var string
     */
    protected $hookName;

    /**
     * Execute hook specified in params and check if the result matches the expected classes if asked.
     *
     * @return array Content returned by modules
     *
     * @throws \Exception if class doesn't match interface or expected classes
     */
    public function find()
    {
        $hookContent = (new HookManager())->exec($this->hookName, $this->params, null, true);
        if (!is_array($hookContent)) {
            $hookContent = array();
        }

        foreach ($hookContent as $moduleName => $moduleContents) {
            if (!is_array($moduleContents)) {
                continue;
            }
            foreach ($moduleContents as $content) {
                // Check data returned if asked
                if (!count($this->expectedInstanceClasses)) {
                    continue;
                }
                if (is_object($content) && !in_array(get_class($content), $this->expectedInstanceClasses)) {
                    throw new \Exception('The module '.$moduleName.' did not return expected class. Was '.get_class($content).' instead of '.implode(' or ', $this->expectedInstanceClasses).'.');
                } elseif (!is_object($content)) {
                    throw new \Exception('The module '.$moduleName.' did not return expected type. Was '.gettype($content).' instead of '.implode(' or ', $this->expectedInstanceClasses).'.');
                }
            }
        }

        return $hookContent;
    }

    /**
     * Present all extra content for templates, meaning converting them as arrays.
     *
     * @return array
     */
    public function present()
    {
        $hookContent = $this->find();
        $presentedContents = array();

        foreach ($hookContent as $moduleName => $moduleContents) {
            foreach ($moduleContents as $content) {
                if (!$content instanceof HookContentClassInterface) {
                    throw new \Exception('The class returned must implement HookContentClassInterface to be presented');
                }

                $presentedContent = $content->toArray();
                $presentedContent['moduleName'] = $moduleName;
                $presentedContents[] = $presentedContent;
            }
        }

        return $presentedContents;
    }

    /**
     * This array contains all the classes expected to be returned
     * by the modules on Hook::exec.
     *
     * @return array
     */
    public function getExpectedInstanceClasses()
    {
        return $this->expectedInstanceClasses;
    }

    /**
     * The hook going to be called when firing find().
     *
     * @return string
     */
    public function getHookName()
    {
        return $this->hookName;
    }

    /**
     * The $params sent to Hook::exec().
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Add an instance of class to be returned by the hook without
     * erasing the other values.
     *
     * @param string|array $expectedInstanceClasses
     *
     * @return \PrestaShopBundle\Service\Hook\Finder
     */
    public function addExpectedInstanceClasses($expectedInstanceClasses)
    {
        if (is_array($expectedInstanceClasses)) {
            array_merge($this->expectedInstanceClasses, $expectedInstanceClasses);
        } else {
            $this->expectedInstanceClasses[] = $expectedInstanceClasses;
        }

        return $this;
    }

    /**
     * Replace all expected classes and types.
     *
     * @param array $expectedInstanceClasses
     *
     * @return \PrestaShopBundle\Service\Hook\Finder
     */
    public function setExpectedInstanceClasses($expectedInstanceClasses)
    {
        $this->expectedInstanceClasses = $expectedInstanceClasses;

        return $this;
    }

    /**
     * Change the hook to be called.
     *
     * @param string $hookName
     *
     * @return \PrestaShopBundle\Service\Hook\Finder
     */
    public function setHookName($hookName)
    {
        $this->hookName = $hookName;

        return $this;
    }

    /**
     * Add a hook param without erasing all the other values.
     *
     * @param array $params
     *
     * @return \PrestaShopBundle\Service\Hook\Finder
     */
    public function addParams($params)
    {
        $this->params = array_merge($this->params, $params);

        return $this;
    }

    /**
     * Replace the params array.
     *
     * @param array $params
     *
     * @return \PrestaShopBundle\Service\Hook\Finder
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }
}
