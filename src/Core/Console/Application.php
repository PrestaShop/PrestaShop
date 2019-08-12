<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Console;

use AppKernel;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application as FrameworkConsoleApplication;

/**
 * The PrestaShop Console application
 */
final class Application extends FrameworkConsoleApplication
{
    public function __construct(KernelInterface $kernel)
    {
        parent::__construct($kernel);

        $this->setName('PrestaShop');
        $this->setVersion(AppKernel::VERSION);

        $inputDefinition = $this->getDefinition();
        $inputDefinition->addOption(new InputOption('employee', '-em', InputOption::VALUE_REQUIRED, 'Specify employee context (id).', null));

        // @see MultiShopCommandListener BC compatible fix to display the options in the Console
        if (!$inputDefinition->hasOption('id_shop')) {
            $inputDefinition->addOption(new InputOption('id_shop', null, InputOption::VALUE_OPTIONAL, 'Specify shop context.'));
        }

        if (!$inputDefinition->hasOption('id_shop_group')) {
            $inputDefinition->addOption(new InputOption('id_shop_group', null, InputOption::VALUE_OPTIONAL, 'Specify shop group context.'));
        }
    }
}
