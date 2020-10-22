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
     * @var array<int, string>
     */
    protected $expectedInstanceClasses = [];

    /**
     * Because we cannot send the same parameters between two different finders,
     * we need a attribute. It will be sent to the Hook::exec() function.
     *
     * @var array
     */
    protected $params = [];

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
            $hookContent = [];
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
                    throw new \Exception('The module ' . $moduleName . ' did not return expected class. Was ' . get_class($content) . ' instead of ' . implode(' or ', $this->expectedInstanceClasses) . '.');
                } elseif (!is_object($content)) {
                    throw new \Exception('The module ' . $moduleName . ' did not return expected type. Was ' . gettype($content) . ' instead of ' . implode(' or ', $this->expectedInstanceClasses) . '.');
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
        $presentedContents = [];

        foreach ($hookContent as $moduleName => $moduleContents) {
            if (!is_array($moduleContents)) {
                continue;
            }
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
     * @return self
     */
    public function addExpectedInstanceClasses($expectedInstanceClasses)
    {
        if (is_array($expectedInstanceClasses)) {
            $this->expectedInstanceClasses = array_merge($this->expectedInstanceClasses, $expectedInstanceClasses);
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
     * @return self
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
     * @return self
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
     * @return self
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
     * @return self
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }
}
