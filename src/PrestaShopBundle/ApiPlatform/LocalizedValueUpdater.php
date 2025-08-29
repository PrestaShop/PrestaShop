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

namespace PrestaShopBundle\ApiPlatform;

use PrestaShopBundle\ApiPlatform\Exception\LocaleNotFoundException;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use PrestaShopBundle\Entity\Repository\LangRepository;
use Symfony\Component\Serializer\Mapping\AttributeMetadataInterface;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;

/**
 * This service can interpret the context values (either set manually ir via the LocalizeValue attribute)
 * and update the provided array by changing its keys, it can switch from an array index by Language ID
 * or by Language locale.
 */
class LocalizedValueUpdater
{
    public function __construct(
        protected LangRepository $languageRepository,
        protected ClassMetadataFactoryInterface $classMetadataFactory
    ) {
    }

    /**
     * @var array<int, string>
     */
    protected array $localesByID;

    /**
     * @var array<string, int>
     */
    protected array $idsByLocale;

    /**
     * @throws LocaleNotFoundException
     */
    public function denormalizeLocalizedValue(mixed $localizedValue, string $propertyName, array $context): mixed
    {
        return $this->updateLocalizedValue($localizedValue, $propertyName, $context, true);
    }

    /**
     * @throws LocaleNotFoundException
     */
    public function normalizeLocalizedValue(mixed $localizedValue, string $propertyName, array $context): mixed
    {
        return $this->updateLocalizedValue($localizedValue, $propertyName, $context, false);
    }

    /**
     * Analyze a class type and extract attributes to check for localized values based on the context associated to
     * each property (combining properties attributes and context parameter). The array returned contains the list of
     * localized value properties, their name is used as the index the value contains the independent context of each
     * property.
     *
     * The returned array can be used for future updates by specifying it in the $context[LocalizedValue::LOCALIZED_VALUE_PARAMETERS]
     *
     * @param string $type Class FQCN
     * @param array $context Serialization context
     *
     * @return array
     */
    public function getLocalizedAttributesContext(string $type, array $context = []): array
    {
        if (!$this->classMetadataFactory->hasMetadataFor($type)) {
            return [];
        }

        $metadata = $this->classMetadataFactory->getMetadataFor($type);
        $localizedAttributesContext = [];
        foreach ($metadata->getAttributesMetadata() as $attributeMetadata) {
            $attributeContext = $this->getAttributeDenormalizationContext($type, $attributeMetadata->getName(), $context);
            if ($this->isAttributeLocalized($attributeContext, $attributeMetadata->getName())) {
                $localizedAttributesContext[$attributeMetadata->getName()] = $attributeContext;
            }
        }

        if (!empty($localizedAttributesContext)) {
            $normalizationMapping = [];
            if (!empty($context[NormalizationMapper::NORMALIZATION_MAPPING])) {
                $normalizationMapping = $context[NormalizationMapper::NORMALIZATION_MAPPING];
            } elseif (!empty($context['operation']) && !empty($context['operation']->getExtraProperties()['CQRSCommandMapping'])) {
                $normalizationMapping = $context['operation']->getExtraProperties()['CQRSCommandMapping'];
            }

            if (!empty($normalizationMapping)) {
                foreach ($localizedAttributesContext as $propertyName => $localizedValueParameter) {
                    $parameterPath = '[' . $propertyName . ']';
                    if (array_key_exists($parameterPath, $normalizationMapping)) {
                        // Transform property path into property name, then replace the attribute context index to match the mapped one
                        $newParameterPath = str_replace(['[', ']'], '', $normalizationMapping[$parameterPath]);
                        $localizedAttributesContext[$newParameterPath] = $localizedValueParameter;
                        unset($localizedAttributesContext[$propertyName]);
                    }
                }
            }
        }

        return $localizedAttributesContext;
    }

    protected function isAttributeLocalized(array $context, string $attribute): bool
    {
        return
            ($context[LocalizedValue::IS_LOCALIZED_VALUE] ?? false)
            || !empty($context[LocalizedValue::LOCALIZED_VALUE_PARAMETERS][$attribute])
        ;
    }

    /**
     * @throws LocaleNotFoundException
     */
    protected function updateLocalizedValue(mixed $localizedValue, string $propertyName, array $context, bool $denormalize): mixed
    {
        if (!is_array($localizedValue) || !$this->isAttributeLocalized($context, $propertyName)) {
            return $localizedValue;
        }

        // Get appropriate key either for normalization or denormalization
        if ($denormalize) {
            if (!empty($context[LocalizedValue::LOCALIZED_VALUE_PARAMETERS][$propertyName][LocalizedValue::DENORMALIZED_KEY])) {
                $localizedValueKey = $context[LocalizedValue::LOCALIZED_VALUE_PARAMETERS][$propertyName][LocalizedValue::DENORMALIZED_KEY];
            } else {
                $localizedValueKey = ($context[LocalizedValue::DENORMALIZED_KEY] ?? LocalizedValue::LOCALE_KEY);
            }
        } else {
            if (!empty($context[LocalizedValue::LOCALIZED_VALUE_PARAMETERS][$propertyName][LocalizedValue::NORMALIZED_KEY])) {
                $localizedValueKey = $context[LocalizedValue::LOCALIZED_VALUE_PARAMETERS][$propertyName][LocalizedValue::NORMALIZED_KEY];
            } else {
                $localizedValueKey = ($context[LocalizedValue::NORMALIZED_KEY] ?? LocalizedValue::LOCALE_KEY);
            }
        }

        // Update the array so that it's based on locale or on ID
        if ($localizedValueKey === LocalizedValue::LOCALE_KEY) {
            $localizedValue = $this->updateLanguageIndexesWithLocales($localizedValue);
        } else {
            $localizedValue = $this->updateLanguageLocalesWithIDs($localizedValue);
        }

        return $localizedValue;
    }

    /**
     * Return the localized array with keys based on locale string value transformed into integer database IDs.
     *
     * @param array $localizedValue
     *
     * @return array
     *
     * @throws LocaleNotFoundException
     */
    protected function updateLanguageLocalesWithIDs(array $localizedValue): array
    {
        $this->fetchLanguagesMapping();
        foreach ($localizedValue as $localeKey => $localeValue) {
            if (is_string($localeKey)) {
                if (!isset($this->idsByLocale[$localeKey])) {
                    throw new LocaleNotFoundException('Locale "' . $localeKey . '" not found.');
                }

                $localizedValue[$this->idsByLocale[$localeKey]] = $localeValue;
                unset($localizedValue[$localeKey]);
            }
        }

        return $localizedValue;
    }

    /**
     * Return the localized array with keys based on integer database IDs transformed into locale string values.
     *
     * @param array $localizedValue
     *
     * @return array
     *
     * @throws LocaleNotFoundException
     */
    protected function updateLanguageIndexesWithLocales(array $localizedValue): array
    {
        $this->fetchLanguagesMapping();
        foreach ($localizedValue as $localeId => $localeValue) {
            if (is_numeric($localeId)) {
                if (!isset($this->localesByID[$localeId])) {
                    throw new LocaleNotFoundException('Locale with ID "' . $localeId . '" not found.');
                }

                $localizedValue[$this->localesByID[$localeId]] = $localeValue;
                unset($localizedValue[$localeId]);
            }
        }

        return $localizedValue;
    }

    /**
     * Fetches the language mapping once and save them in local property for better performance.
     *
     * @return void
     */
    protected function fetchLanguagesMapping(): void
    {
        if (!isset($this->localesByID) || !isset($this->idsByLocale)) {
            $this->localesByID = [];
            $this->idsByLocale = [];
            foreach ($this->languageRepository->getMapping() as $langId => $language) {
                $this->localesByID[(int) $langId] = $language['locale'];
                $this->idsByLocale[$language['locale']] = (int) $langId;
            }
        }
    }

    protected function getAttributeDenormalizationContext(string $class, string $attribute, array $context): array
    {
        $context['deserialization_path'] = ($context['deserialization_path'] ?? false) ? $context['deserialization_path'] . '.' . $attribute : $attribute;

        if (null === $metadata = $this->getAttributeMetadata($class, $attribute)) {
            return $context;
        }

        return array_merge($context, $metadata->getDenormalizationContextForGroups($this->getGroups($context)));
    }

    protected function getAttributeMetadata(object|string $objectOrClass, string $attribute): ?AttributeMetadataInterface
    {
        return $this->classMetadataFactory->getMetadataFor($objectOrClass)->getAttributesMetadata()[$attribute] ?? null;
    }

    protected function getGroups(array $context): array
    {
        $groups = $context['groups'] ?? $this->defaultContext['groups'] ?? [];

        return \is_scalar($groups) ? (array) $groups : $groups;
    }
}
