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

namespace PrestaShopBundle\Translation\Provider\Factory;

use PrestaShopBundle\Translation\Loader\DatabaseTranslationReader;
use PrestaShopBundle\Translation\Provider\MailsBodyProvider;
use PrestaShopBundle\Translation\Provider\ProviderInterface;
use PrestaShopBundle\Translation\Provider\Type\MailsBodyType;
use PrestaShopBundle\Translation\Provider\Type\TypeInterface;

class MailsBodyProviderFactory implements ProviderFactoryInterface
{
    /**
     * @var DatabaseTranslationReader
     */
    private $databaseTranslationReader;
    /**
     * @var string
     */
    private $resourceDirectory;

    public function __construct(DatabaseTranslationReader $databaseTranslationReader, string $resourceDirectory)
    {
        $this->databaseTranslationReader = $databaseTranslationReader;
        $this->resourceDirectory = $resourceDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function implements(TypeInterface $providerType): bool
    {
        return $providerType instanceof MailsBodyType;
    }

    /**
     * {@inheritdoc}
     */
    public function build(TypeInterface $providerType): ProviderInterface
    {
        if (!$this->implements($providerType)) {
            throw new \RuntimeException(sprintf('Invalid provider type given: %s', get_class($providerType)));
        }

        return new MailsBodyProvider($this->databaseTranslationReader, $this->resourceDirectory);
    }
}
