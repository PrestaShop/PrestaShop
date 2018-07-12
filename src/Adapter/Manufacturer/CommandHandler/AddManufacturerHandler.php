<?php

/**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Manufacturer\CommandHandler;

use Exception;
use Manufacturer;
use PrestaShop\PrestaShop\Adapter\Language\LanguageDataProvider;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\AddManufacturerCommand;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Command\Handler\AddManufacturerInterface;
use PrestaShop\PrestaShop\Core\Domain\Manufacturer\Exception\CannotAddManufacturerException;

/**
 * Adds a new manufacturer
 */
class AddManufacturerHandler implements AddManufacturerInterface
{
    /**
     * @var AddManufacturerCommand
     */
    private $command;

    /**
     * @var LanguageDataProvider
     */
    private $languagesDataProvider;

    /**
     * Index of installed languages as $langId => true
     * @var array
     */
    private $installedLanguages = [];

    /**
     * @param AddManufacturerCommand $command
     *
     * @throws CannotAddManufacturerException
     *
     * @return int Id of the added Manufacturer
     */
    public function handle(AddManufacturerCommand $command)
    {
        $this->command = $command;
        try {
            $this->loadInstalledLanguages();

            return $this->buildManufacturer();
        } catch (Exception $e) {
            throw new CannotAddManufacturerException(
                "Unable to add a new Manufacturer",
                0,
                $e
            );
        }
    }

    /**
     * @return int Id of the new Manufacturer
     *
     * @throws Exception
     */
    private function buildManufacturer() {
        $command = $this->command;
        $entity = new Manufacturer();

        $entity->name = $command->getName();

        $this->assignMultilangProperties($entity);

        $entity->validateFields();

        $entity->add();

        if ($entity->id <= 0) {
            throw new Exception(
                sprintf('Invalid entity id after creation: %s', var_export($entity->id, true))
            );
        }

        return $entity->id;
    }

    /**
     * @param Manufacturer $entity
     *
     * @throws DomainConstraintException
     */
    private function assignMultilangProperties(Manufacturer $entity)
    {
        $multiLangProperties = [
            'description'       => $this->command->getDescriptions(),
            'short_description' => $this->command->getShortDescriptions(),
            'meta_title'        => $this->command->getMetaTitles(),
            'meta_keywords'     => $this->command->getMetaTitles(),
            'meta_description'  => $this->command->getMetaDescriptions(),
            'active'            => $this->command->isActive(),
        ];

        foreach ($multiLangProperties as $propName => $dataToAssign) {
            foreach ($dataToAssign as $langId => $description) {
                if (!isset($this->installedLanguages[$langId])) {
                    throw new DomainConstraintException(
                        sprintf("Language id %s not installed", $langId),
                        DomainConstraintException::LANGUAGE_ID_NOT_INSTALLED
                    );
                }
                $entity->$propName[$langId] = $description;
            }
        }

    }

    /**
     * Builds an index of installed languages
     */
    private function loadInstalledLanguages()
    {
        $installedLanguages = $this->languagesDataProvider->getLanguages(true, false, true);

        foreach (array_values($installedLanguages) as $langId) {
            $this->installedLanguages[$langId] = true;
        }
    }
}
