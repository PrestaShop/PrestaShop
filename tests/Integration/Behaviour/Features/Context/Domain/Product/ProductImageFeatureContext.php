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

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Product;

use PrestaShop\PrestaShop\Core\Domain\Product\Command\AddProductImageCommand;
use RuntimeException;

class ProductImageFeatureContext extends AbstractProductFeatureContext
{
    /**
     * @When I add new product :productReference image :fileName
     *
     * @param string $productReference
     * @param string $fileName
     */
    public function uploadImage(string $productReference, string $fileName)
    {
        //@todo: behats database contains empty ImageType.
        $pathName = $this->uploadDummyFile($fileName);

        $this->getCommandBus()->handle(new AddProductImageCommand(
            $this->getSharedStorage()->get($productReference),
            $fileName,
            $pathName
        ));
    }

    //@todo: homogenize with attachments context method
    private function uploadDummyFile(string $fileName): string
    {
        $source = _PS_ROOT_DIR_ . '/tests/Resources/dummyFile/' . $fileName;

        if (!is_file($source)) {
            throw new RuntimeException('%s is not a file', $source);
        }

        $destination = tempnam(sys_get_temp_dir(), 'PS_TEST_');
        copy($source, $destination);

        return $destination;
    }
}
