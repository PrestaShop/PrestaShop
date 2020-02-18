<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader as BaseYamlFileLoader;

class DependencyYamlFileLoader extends BaseYamlFileLoader
{
    /** @var array */
    private $dependencies = [];

    /**
     * {@inheritdoc}
     */
    protected function setDefinition($id, Definition $definition)
    {
        if (!in_array($id, $this->dependencies)) {
            return;
        }

        // Add sub dependencies
        if ($definition instanceof ChildDefinition) {
            $this->addDependency($definition->getParent());
        }

        parent::setDefinition($id, $definition);
    }

    /**
     * @param $id
     */
    public function addDependency($id)
    {
        if (in_array($id, $this->dependencies)) {
            return;
        }

        $this->dependencies[] = $id;
    }
}
