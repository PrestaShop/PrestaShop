<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace PrestaShopBundle\Install;

use ConfigurationTest;
use Requirement;
use SymfonyRequirements;

require_once __DIR__ . '/../../../var/SymfonyRequirements.php';

class System extends AbstractInstall
{
    public function checkRequiredTests()
    {
        return self::checkTests(ConfigurationTest::getDefaultTests(), 'required');
    }

    public function checkOptionalTests()
    {
        return self::checkTests(ConfigurationTest::getDefaultTestsOp(), 'optional');
    }

    //get symfony requirements
    public function checkSf2Requirements()
    {
        $symfonyRequirements = new SymfonyRequirements();
        $errors = $symfonyRequirements->getFailedRequirements();

        return $errors;
    }

    //get symfony recommendations
    public function checkSf2Recommendations()
    {
        $symfonyRequirements = new SymfonyRequirements();

        $failedRecommendations = $symfonyRequirements->getFailedRecommendations();

        return array_filter($failedRecommendations, function (Requirement $requirement) {
            if ($requirement->getTestMessage() === 'Requirements file should be up-to-date') {
                // this warning is not relevant
                return false;
            }

            return true;
        });
    }

    public function checkTests($list, $type)
    {
        $tests = ConfigurationTest::check($list);

        $success = true;
        foreach ($tests as $result) {
            $success &= ($result == 'ok') ? true : false;
        }

        return array(
            'checks' => $tests,
            'success' => $success,
        );
    }
}
