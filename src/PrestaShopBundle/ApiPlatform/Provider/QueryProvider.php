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

namespace PrestaShopBundle\ApiPlatform\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShopBundle\ApiPlatform\Exception\NoExtraPropertiesFoundException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class QueryProvider implements ProviderInterface
{
    public function __construct(private CommandBusInterface $queryBus, private DenormalizerInterface $serializer)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = [])
    {
        $extraProperties = $operation->getExtraProperties();

        $query = array_key_exists('query', $extraProperties) ? $extraProperties['query'] : null;
        $command = array_key_exists('command', $extraProperties) ? $extraProperties['command'] : null;
        $dto = array_key_exists('dto', $extraProperties) ? $extraProperties['dto'] : null;

        if (null !== $query) {
            return $this->queryBus->handle(new $query(...$uriVariables));
        } elseif (null !== $command) {
            // the command case has yet to be implemented.
            return null;
        } elseif (null !== $dto) {
            $coreObject = $this->queryBus->handle(new $dto(...$uriVariables));
            // We use get_object_var to convert the ObjectModel into an array and then denormalize it into the DTO.
            // We handle things this way because we are unable to normalize an ObjectModel
            // directly with the symfony serializer.
            return $this->serializer->denormalize(
                get_object_vars($coreObject),
                $extraProperties['denormalizer'],
                context: [ObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true]
            );
        } else {
            throw new NoExtraPropertiesFoundException();
        }
    }
}
