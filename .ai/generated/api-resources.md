# Admin API resources index (generated 2026-10-20)
# 139 resource classes · 36 domains · source: modules/ps_apiresources/src/ApiPlatform/Resources
#
# One line per operation: METHOD uri → resource class (operation attribute). Sub objects live in {Domain}/Dto/.
# Conventions: modules/ps_apiresources/.ai/CONTEXT.md · review checklist: modules/ps_apiresources/.ai/REVIEW.md

## Address
- DELETE /addresses/{addressId} → Address (CQRSDelete)
- GET (collection) /addresses → AddressList (PaginatedList)
- DELETE /addresses/bulk-delete → BulkAddresses (CQRSDelete)
- PATCH /addresses/carts/{cartId} → CartAddress (CQRSPartialUpdate)
- GET /addresses/customers/{addressId} → CustomerAddress (CQRSGet)
- POST /addresses/customers → CustomerAddress (CQRSCreate)
- PATCH /addresses/customers/{addressId} → CustomerAddress (CQRSPartialUpdate)
- GET /addresses/manufacturers/{addressId} → ManufacturerAddress (CQRSGet)
- POST /addresses/manufacturers → ManufacturerAddress (CQRSCreate)
- PATCH /addresses/manufacturers/{addressId} → ManufacturerAddress (CQRSPartialUpdate)
- PATCH /addresses/orders/{orderId} → OrderAddress (CQRSPartialUpdate)

## ApiClient
- GET /api-clients/{apiClientId} → ApiClient (CQRSGet)
- DELETE /api-clients/{apiClientId} → ApiClient (CQRSDelete)
- POST /api-clients → ApiClient (CQRSCreate)
- PATCH /api-clients/{apiClientId} → ApiClient (CQRSPartialUpdate)
- GET (collection) /api-clients → ApiClientList (PaginatedList)

## Attachment
- GET /attachments/{attachmentId}/files → AttachmentFile (CQRSGet)
- GET /attachments/{attachmentId}/information → AttachmentInformation (CQRSGet)
- GET (collection) /attachments/search → FoundAttachment (CQRSGetCollection)

## Attribute
- GET /attributes/attributes/{attributeId} → Attribute (CQRSGet)
- POST /attributes/attributes → Attribute (CQRSCreate)
- PATCH /attributes/attributes/{attributeId} → Attribute (CQRSPartialUpdate)
- DELETE /attributes/attributes/{attributeId} → Attribute (CQRSDelete)
- GET /attributes/groups/{attributeGroupId} → AttributeGroup (CQRSGet)
- POST /attributes/groups → AttributeGroup (CQRSCreate)
- PATCH /attributes/groups/{attributeGroupId} → AttributeGroup (CQRSPartialUpdate)
- DELETE /attributes/groups/{attributeGroupId} → AttributeGroup (CQRSDelete)
- GET (collection) /attributes/groups → AttributeGroupList (PaginatedList)
- PATCH /attributes/groups/positions → AttributeGroupPosition (UpdatePosition)
- GET (collection) /attributes/groups-with-attributes → AttributeGroupWithAttributes (CQRSGetCollection)
- GET (collection) /attributes/groups/{attributeGroupId}/attributes → AttributeList (PaginatedList)
- PATCH /attributes/groups/{attributeGroupId}/attributes/positions → AttributePosition (UpdatePosition)
- DELETE /attributes/attributes/bulk-delete → BulkAttribute (CQRSDelete)
- DELETE /attributes/groups/bulk-delete → BulkAttributeGroups (CQRSDelete)

## Carrier
- GET /carriers/{carrierId} → Carrier (CQRSGet)
- POST /carriers → Carrier (CQRSCreate)
- POST /carriers/{carrierId} → Carrier (CQRSCreate)
- PATCH /carriers/{carrierId}/set-tax-rule-group → Carrier (CQRSPartialUpdate)
- GET (collection) /carriers → CarrierList (PaginatedList)
- GET /carriers/{carrierId}/ranges → CarrierRanges (CQRSGet)
- PATCH /carriers/{carrierId}/ranges → CarrierRanges (CQRSPartialUpdate)
- GET /carriers/search-compatible-carriers → CompatibleCarriers (CQRSGet)

## Cart
- PUT /carts/{cartId}/emails → CartEmail (CQRSUpdate)

## CartRule
- GET (collection) /cart-rules/search → FoundCartRule (CQRSGetCollection)

## Category
- DELETE /categories/bulk-delete/{deleteMode} → BulkDeleteCategories (CQRSDelete)
- PUT /categories/bulk-update-status → BulkUpdateStatusCategories (CQRSUpdate)
- GET /categories/{categoryId} → Category (CQRSGet)
- POST /categories → Category (CQRSCreate)
- PATCH /categories/{categoryId} → Category (CQRSPartialUpdate)
- PATCH /categories/{categoryId}/status → Category (CQRSPartialUpdate)
- DELETE /categories/{categoryId}/cover → Category (CQRSDelete)
- DELETE /categories/{categoryId}/thumbnail → Category (CQRSDelete)
- DELETE /categories/{categoryId}/{mode} → CategoryDelete (CQRSDelete)
- GET (collection) /categories → CategoryList (PaginatedList)

## Contact
- GET /contacts/{contactId} → Contact (CQRSGet)
- POST /contacts → Contact (CQRSCreate)
- PATCH /contacts/{contactId} → Contact (CQRSPartialUpdate)
- GET (collection) /contacts → ContactList (PaginatedList)

## Country
- POST /countries → Country (CQRSCreate)
- GET /countries/{countryId} → Country (CQRSGet)
- PATCH /countries/{countryId} → Country (CQRSPartialUpdate)
- DELETE /countries/{countryId} → Country (CQRSDelete)
- GET (collection) /countries → CountryList (PaginatedList)

## Customer
- PUT /customers/bulk-disable → BulkCustomers (CQRSUpdate)
- PUT /customers/bulk-enable → BulkCustomers (CQRSUpdate)
- DELETE /customers/bulk-delete → BulkDeleteCustomers (CQRSDelete)
- POST /customers → Customer (CQRSCreate)
- GET /customers/{customerId} → Customer (CQRSGet)
- PATCH /customers/{customerId} → Customer (CQRSPartialUpdate)
- PUT /customers/{customerId}/transform-to-customer → Customer (CQRSUpdate)
- DELETE /customers/{customerId} → Customer (CQRSDelete)
- GET (collection) /customers/{customerId}/carts → CustomerCart (CQRSGetCollection)
- POST /customers/{customerId}/carts → CustomerCart (CQRSCreate)
- GET /customers/{customerId}/details → CustomerDetails (CQRSGet)
- PATCH /customers/{customerId}/set-private-note → CustomerDetails (CQRSPartialUpdate)
- GET /customers/groups/{customerGroupId} → CustomerGroup (CQRSGet)
- POST /customers/groups → CustomerGroup (CQRSCreate)
- PATCH /customers/groups/{customerGroupId} → CustomerGroup (CQRSPartialUpdate)
- DELETE /customers/groups/{customerGroupId} → CustomerGroup (CQRSDelete)
- GET (collection) /customers/groups → CustomerGroupList (PaginatedList)
- GET (collection) /customers → CustomerList (PaginatedList)
- GET (collection) /customers/{customerId}/orders → CustomerOrder (CQRSGetCollection)
- GET /customers/required-fields → CustomerRequiredFields (CQRSGet)
- PUT /customers/required-fields → CustomerRequiredFields (CQRSUpdate)
- GET (collection) /customers/search → FoundCustomer (CQRSGetCollection)

## Discount
- DELETE /discounts/bulk-delete → BulkDeleteDiscounts (CQRSDelete)
- PATCH /discounts/bulk-update-status → BulkUpdateStatusDiscounts (CQRSPartialUpdate)
- GET /discounts/{discountId} → Discount (CQRSGet)
- POST /discounts → Discount (CQRSCreate)
- PATCH /discounts/{discountId} → Discount (CQRSPartialUpdate)
- DELETE /discounts/{discountId} → Discount (CQRSDelete)
- POST /discounts/{discountId}/duplicate → Discount (CQRSCreate)
- GET (collection) /discounts → DiscountList (PaginatedList)
- GET (collection) /discounts/types → DiscountTypeList (CQRSGetCollection)

## Employee
- POST /employees/send-password-reset-email → EmployeePasswordReset (CQRSCreate)

## ExtraPropertyDefinition
- DELETE /extra-property-definitions/bulk-delete → BulkDeleteExtraPropertyDefinitions (CQRSDelete)
- GET /extra-property-definitions/{extraPropertyDefinitionId} → ExtraPropertyDefinition (CQRSGet)
- POST /extra-property-definitions → ExtraPropertyDefinition (CQRSCreate)
- PATCH /extra-property-definitions/{extraPropertyDefinitionId} → ExtraPropertyDefinition (CQRSPartialUpdate)
- DELETE /extra-property-definitions/{extraPropertyDefinitionId} → ExtraPropertyDefinition (CQRSDelete)
- GET (collection) /extra-property-definitions → ExtraPropertyDefinitionList (PaginatedList)

## Feature
- DELETE /features/values/bulk-delete → BulkFeatureValues (CQRSDelete)
- DELETE /features/bulk-delete → BulkFeatures (CQRSDelete)
- GET /features/{featureId} → Feature (CQRSGet)
- POST /features → Feature (CQRSCreate)
- PATCH /features/{featureId} → Feature (CQRSPartialUpdate)
- DELETE /features/{featureId} → Feature (CQRSDelete)
- GET (collection) /features → FeatureList (PaginatedList)
- GET /features/values/{featureValueId} → FeatureValue (CQRSGet)
- POST /features/values → FeatureValue (CQRSCreate)
- PATCH /features/values/{featureValueId} → FeatureValue (CQRSPartialUpdate)
- DELETE /features/values/{featureValueId} → FeatureValue (CQRSDelete)
- GET (collection) /features/{featureId}/values → FeatureValueList (PaginatedList)

## Hook
- PUT /hooks/{hookId}/status → Hook (CQRSUpdate)
- GET /hooks/{hookId} → Hook (CQRSGet)
- GET /hooks/{hookId}/status → Hook (CQRSGet)
- GET (collection) /hooks → Hook (PaginatedList)

## ImageType
- DELETE /image-types/bulk-delete → BulkDeleteImageTypes (CQRSDelete)
- DELETE /image-types/{imageTypeId}/images → DeleteImagesFromType (CQRSDelete)
- POST /image-types → ImageType (CQRSCreate)
- DELETE /image-types/{imageTypeId} → ImageType (CQRSDelete)
- GET /image-types/{imageTypeId} → ImageType (CQRSGet)
- PATCH /image-types/{imageTypeId} → ImageType (CQRSPartialUpdate)
- PUT /image-types/regenerate-thumbnails → RegenerateThumbnails (CQRSUpdate)

## Manufacturer
- PUT /manufacturers/bulk-delete → BulkManufacturers (CQRSUpdate)
- GET /manufacturers/{manufacturerId} → Manufacturer (CQRSGet)
- POST /manufacturers → Manufacturer (CQRSCreate)
- PATCH /manufacturers/{manufacturerId} → Manufacturer (CQRSPartialUpdate)
- DELETE /manufacturers/{manufacturerId} → Manufacturer (CQRSDelete)
- GET (collection) /manufacturers → ManufacturerList (PaginatedList)

## Module
- PUT /modules/bulk-update-status → BulkModules (CQRSUpdate)
- PUT /modules/bulk-uninstall → BulkModules (CQRSUpdate)
- GET /modules/{technicalName} → Module (CQRSGet)
- PUT /modules/{technicalName}/status → Module (CQRSUpdate)
- PUT /modules/{technicalName}/install → Module (CQRSUpdate)
- GET (collection) /modules → Module (PaginatedList)
- PATCH /modules/{technicalName}/reset → ResetModule (CQRSPartialUpdate)
- PUT /modules/{technicalName}/uninstall → UninstallModule (CQRSUpdate)
- PUT /modules/{technicalName}/upgrade → UpgradeModule (CQRSUpdate)
- POST /modules/upload-source → UploadModule (CQRSCreate)
- POST /modules/upload-archive → UploadModule (CQRSCreate)

## OrderMessage
- DELETE /order-messages/bulk-delete → BulkDeleteOrderMessages (CQRSDelete)
- POST /order-messages → OrderMessage (CQRSCreate)
- DELETE /order-messages/{orderMessageId} → OrderMessage (CQRSDelete)
- GET /order-messages/{orderMessageId} → OrderMessage (CQRSGet)
- PATCH /order-messages/{orderMessageId} → OrderMessage (CQRSPartialUpdate)
- GET (collection) /order-messages → OrderMessageList (PaginatedList)

## OrderReturn
- GET /order-returns/{orderReturnId} → OrderReturn (CQRSGet)
- PATCH /order-returns/{orderReturnId} → OrderReturn (CQRSPartialUpdate)

## OrderReturnState
- DELETE /order-return-states/bulk-delete → BulkDeleteOrderReturnState (CQRSDelete)
- POST /order-return-states → OrderReturnState (CQRSCreate)
- GET /order-return-states/{orderReturnStateId} → OrderReturnState (CQRSGet)
- PATCH /order-return-states/{orderReturnStateId} → OrderReturnState (CQRSPartialUpdate)
- DELETE /order-return-states/{orderReturnStateId} → OrderReturnState (CQRSDelete)
- GET (collection) /order-return-states → OrderReturnStateList (PaginatedList)

## Product
- GET /products/combinations/{combinationId} → Combination (CQRSGet)
- GET /products/{productId}/combination-ids → CombinationIdList (CQRSGet)
- GET (collection) /products/{productId}/combinations → CombinationList (CQRSPaginate)
- PATCH /products/combinations/{combinationId}/stock → CombinationStock (CQRSPartialUpdate)
- GET (collection) /products/search → FoundProduct (CQRSGetCollection)
- GET (collection) /products/free-gift-candidates → FreeGiftCandidate (CQRSGetCollection)
- POST /products/{productId}/generate-combinations → GenerateCombinations (CQRSCreate)
- POST /products/{productId}/images → NewProductImage (CQRSCreate)
- GET /products/{productId} → Product (CQRSGet)
- POST /products → Product (CQRSCreate)
- PATCH /products/{productId} → Product (CQRSPartialUpdate)
- DELETE /products/{productId} → Product (CQRSDelete)
- GET (collection) /products/{productId}/attribute-groups → ProductAttributeGroupList (CQRSGetCollection)
- GET /products/{productId}/carriers → ProductCarrier (CQRSGet)
- PUT /products/{productId}/carriers → ProductCarrier (CQRSUpdate)
- PATCH /products/{productId}/assign-to-categories → ProductCategory (CQRSPartialUpdate)
- PUT /products/{productId}/categories → ProductCategory (CQRSUpdate)
- DELETE /products/{productId}/categories → ProductCategory (CQRSDelete)
- GET /products/images/{imageId} → ProductImage (CQRSGet)
- PUT /products/images/{imageId} → ProductImage (CQRSUpdate)
- DELETE /products/images/{imageId} → ProductImage (CQRSDelete)
- GET (collection) /products/{productId}/images → ProductImageList (CQRSGetCollection)
- GET (collection) /products → ProductList (PaginatedList)
- PATCH /products/{productId}/shops → ProductShops (CQRSPartialUpdate)
- PATCH /products/{productId}/stock → ProductStock (CQRSPartialUpdate)
- GET (collection) /products/{productId}/stock-movements → ProductStockMovements (CQRSGetCollection)
- GET /products/{productId}/suppliers → ProductSuppliers (CQRSGet)
- PUT /products/{productId}/suppliers → ProductSuppliers (CQRSUpdate)
- PATCH /products/{productId}/suppliers → ProductSuppliers (CQRSPartialUpdate)
- DELETE /products/{productId}/suppliers → ProductSuppliers (CQRSDelete)
- PUT /products/{productId}/default-supplier → ProductSuppliers (CQRSUpdate)
- GET /products/{productId}/shop-images → ShopProductImages (CQRSGet)
- PUT /products/{productId}/shop-images → ShopProductImages (CQRSUpdate)
- POST /products/{productId}/virtual-files → VirtualProductFile (CQRSCreate)
- GET /products/virtual-files/{virtualProductFileId} → VirtualProductFile (CQRSGet)
- PUT /products/virtual-files/{virtualProductFileId} → VirtualProductFile (CQRSUpdate)
- DELETE /products/virtual-files/{virtualProductFileId} → VirtualProductFile (CQRSDelete)

## Profile
- GET /profiles/{profileId} → Profile (CQRSGet)
- POST /profiles → Profile (CQRSCreate)
- DELETE /profiles/{profileId} → Profile (CQRSDelete)

## SearchAlias
- DELETE /search-aliases/bulk-delete → BulkDeleteSearchAliases (CQRSDelete)
- GET /search-aliases/{searchTerm} → SearchAlias (CQRSGet)
- POST /search-aliases → SearchAlias (CQRSCreate)
- PUT /search-aliases/{searchTerm} → SearchAlias (CQRSUpdate)
- GET (collection) /search-aliases → SearchAliasList (PaginatedList)

## SearchEngine
- DELETE /search-engines/bulk-delete → BulkDeleteSearchEngines (CQRSDelete)
- DELETE /search-engines/{searchEngineId} → DeleteSearchEngine (CQRSDelete)
- POST /search-engines → SearchEngine (CQRSCreate)
- GET /search-engines/{searchEngineId} → SearchEngine (CQRSGet)
- PATCH /search-engines/{searchEngineId} → SearchEngine (CQRSPartialUpdate)
- GET (collection) /search-engines → SearchEngineList (PaginatedList)

## ShowcaseCard
- GET /showcase-cards/{showcaseCardName}/{employeeId} → ShowcaseCard (CQRSGet)
- PUT /showcase-cards/{showcaseCardName}/{employeeId}/close → ShowcaseCard (CQRSUpdate)

## Store
- DELETE /stores/bulk-delete → BulkDeleteStores (CQRSDelete)
- PUT /stores/bulk-update-status → BulkUpdateStatusStores (CQRSUpdate)
- DELETE /stores/{storeId} → Store (CQRSDelete)
- GET /stores/{storeId} → Store (CQRSGet)
- PUT /stores/{storeId}/toggle-status → Store (CQRSUpdate)

## Supplier
- DELETE /suppliers/bulk-delete → BulkSuppliers (CQRSDelete)
- PUT /suppliers/bulk-disable → BulkSuppliers (CQRSUpdate)
- PUT /suppliers/bulk-enable → BulkSuppliers (CQRSUpdate)
- POST /suppliers → Supplier (CQRSCreate)
- DELETE /suppliers/{supplierId} → Supplier (CQRSDelete)
- DELETE /suppliers/{supplierId}/logo → Supplier (CQRSDelete)
- GET /suppliers/{supplierId} → Supplier (CQRSGet)
- PATCH /suppliers/{supplierId} → Supplier (CQRSPartialUpdate)
- PUT /suppliers/{supplierId}/toggle-status → Supplier (CQRSUpdate)
- GET (collection) /suppliers → SupplierList (PaginatedList)

## Tab
- PUT /tabs/{className} → Tab (CQRSUpdate)

## Tax
- PUT /taxes/bulk-delete → BulkTaxDelete (CQRSUpdate)
- PUT /taxes/bulk-set-status → BulkTaxToggleStatus (CQRSUpdate)
- POST /taxes → Tax (CQRSCreate)
- DELETE /taxes/{taxId} → Tax (CQRSDelete)
- GET /taxes/{taxId} → Tax (CQRSGet)
- PATCH /taxes/{taxId} → Tax (CQRSPartialUpdate)
- GET (collection) /taxes → TaxList (PaginatedList)

## TaxRule
- POST /tax-rules → TaxRule (CQRSCreate)
- DELETE /tax-rules/{taxRuleId} → TaxRule (CQRSDelete)
- GET (collection) /tax-rules → TaxRuleList (PaginatedList)

## TaxRulesGroup
- DELETE /tax-rules-groups/bulk-delete → BulkDeleteTaxRulesGroup (CQRSDelete)
- PUT /tax-rules-groups/bulk-update-status → BulkUpdateStatusTaxRulesGroup (CQRSUpdate)
- POST /tax-rules-groups → TaxRulesGroup (CQRSCreate)
- DELETE /tax-rules-groups/{taxRulesGroupId} → TaxRulesGroup (CQRSDelete)
- GET /tax-rules-groups/{taxRulesGroupId} → TaxRulesGroup (CQRSGet)
- PATCH /tax-rules-groups/{taxRulesGroupId} → TaxRulesGroup (CQRSPartialUpdate)
- PATCH /tax-rules-groups/{taxRulesGroupId}/set-status → TaxRulesGroup (CQRSPartialUpdate)
- GET (collection) /tax-rules-groups → TaxRulesGroupList (PaginatedList)

## Title
- DELETE /titles/bulk-delete → BulkDeleteTitles (CQRSDelete)
- POST /titles → Title (CQRSCreate)
- DELETE /titles/{titleId} → Title (CQRSDelete)
- GET /titles/{titleId} → Title (CQRSGet)
- PATCH /titles/{titleId} → Title (CQRSPartialUpdate)
- GET (collection) /titles → TitleList (PaginatedList)

## WebserviceKey
- POST /webservice-keys → WebserviceKey (CQRSCreate)
- GET /webservice-keys/{webserviceKeyId} → WebserviceKey (CQRSGet)
- PATCH /webservice-keys/{webserviceKeyId} → WebserviceKey (CQRSPartialUpdate)
- GET (collection) /webservice-keys → WebserviceKeyList (PaginatedList)

## Zone
- DELETE /zones/bulk-delete → BulkDeleteZones (CQRSDelete)
- PUT /zones/bulk-update-status → BulkUpdateStatusZone (CQRSUpdate)
- POST /zones → Zone (CQRSCreate)
- DELETE /zones/{zoneId} → Zone (CQRSDelete)
- GET /zones/{zoneId} → Zone (CQRSGet)
- PUT /zones/{zoneId}/toggle-status → Zone (CQRSUpdate)
- PUT /zones/{zoneId} → Zone (CQRSUpdate)
- GET (collection) /zones → ZoneList (PaginatedList)

