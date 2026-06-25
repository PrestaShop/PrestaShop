# CQRS Index (generated 2026-04-25)
# 360 commands · 126 queries · 62 top-level domains
#
# Sub-domain shown in [brackets] when command/query lives below the top-level domain dir.

## Address
### Commands
- AbstractEditAddressCommand
- AddCustomerAddressCommand
- AddManufacturerAddressCommand
- BulkDeleteAddressCommand
- DeleteAddressCommand
- EditCartAddressCommand
- EditCustomerAddressCommand
- EditManufacturerAddressCommand
- EditOrderAddressCommand
- SetRequiredFieldsForAddressCommand
### Queries
- GetCustomerAddressForEditing
- GetManufacturerAddressForEditing
- GetRequiredFieldsForAddress

## Alias
### Commands
- AddSearchTermAliasesCommand
- BulkDeleteSearchTermsAliasesCommand
- DeleteSearchTermAliasesCommand
- UpdateSearchTermAliasesCommand
### Queries
- GetAliasForEditing
- GetAliasesBySearchTermForEditing
- SearchForSearchTerm

## ApiClient
### Commands
- AddApiClientCommand
- DeleteApiClientCommand
- EditApiClientCommand
- ForceApiClientSecretCommand
- GenerateApiClientSecretCommand
### Queries
- GetApiClientForEditing

## Attachment
### Commands
- AddAttachmentCommand
- BulkDeleteAttachmentsCommand
- DeleteAttachmentCommand
- EditAttachmentCommand
### Queries
- GetAttachment
- GetAttachmentForEditing
- GetAttachmentInformation
- SearchAttachment

## AttributeGroup
### Commands
- AddAttributeCommand  [Attribute]
- BulkDeleteAttributeCommand  [Attribute]
- DeleteAttributeCommand  [Attribute]
- EditAttributeCommand  [Attribute]
- AddAttributeGroupCommand
- BulkDeleteAttributeGroupCommand
- DeleteAttributeGroupCommand
- DeleteAttributeTextureImageCommand
- EditAttributeGroupCommand
### Queries
- GetAttributeForEditing  [Attribute]
- GetAttributeGroupForEditing
- GetAttributeGroupList

## Carrier
### Commands
- AddCarrierCommand
- BulkDeleteCarrierCommand
- BulkToggleCarrierStatusCommand
- DeleteCarrierCommand
- EditCarrierCommand
- SetCarrierRangesCommand
- SetCarrierTaxRuleGroupCommand
- ToggleCarrierIsFreeCommand
- ToggleCarrierStatusCommand
### Queries
- GetAvailableCarriers
- GetCarrierForEditing
- GetCarrierRanges
- GetCarriersForProduct

## Cart
### Commands
- AddCartRuleToCartCommand
- AddCustomizationCommand
- AddProductToCartCommand
- BulkDeleteCartCommand
- CreateEmptyCustomerCartCommand
- DeleteCartCommand
- RemoveCartRuleFromCartCommand
- RemoveProductFromCartCommand
- SendCartToCustomerCommand
- UpdateCartAddressesCommand
- UpdateCartCarrierCommand
- UpdateCartCurrencyCommand
- UpdateCartDeliverySettingsCommand
- UpdateCartLanguageCommand
- UpdateProductPriceInCartCommand
- UpdateProductQuantityInCartCommand
### Queries
- GetCartForOrderCreation
- GetCartForViewing
- GetLastEmptyCustomerCart

## CartRule
### Queries
- SearchCartRules

## CatalogPriceRule
### Commands
- AddCatalogPriceRuleCommand
- BulkDeleteCatalogPriceRuleCommand
- DeleteCatalogPriceRuleCommand
- EditCatalogPriceRuleCommand
### Queries
- GetCatalogPriceRuleForEditing
- GetCatalogPriceRuleListForProduct

## Category
### Commands
- AddCategoryCommand
- AddRootCategoryCommand
- BulkDeleteCategoriesCommand
- BulkDisableCategoriesCommand
- BulkEnableCategoriesCommand
- BulkUpdateCategoriesStatusCommand
- DeleteCategoryCommand
- DeleteCategoryCoverImageCommand
- DeleteCategoryThumbnailImageCommand
- EditCategoryCommand
- EditRootCategoryCommand
- SetCategoryIsEnabledCommand
- UpdateCategoryPositionCommand
### Queries
- GetCategoriesTree
- GetCategoryForEditing
- GetCategoryIsEnabled

## CmsPage
### Commands
- AddCmsPageCommand
- BulkDeleteCmsPageCommand
- BulkDisableCmsPageCommand
- BulkEnableCmsPageCommand
- DeleteCmsPageCommand
- EditCmsPageCommand
- ToggleCmsPageStatusCommand
### Queries
- GetCmsCategoryIdForRedirection
- GetCmsPageForEditing

## CmsPageCategory
### Commands
- AbstractBulkCmsPageCategoryCommand
- AbstractCmsPageCategoryCommand
- AddCmsPageCategoryCommand
- BulkDeleteCmsPageCategoryCommand
- BulkDisableCmsPageCategoryCommand
- BulkEnableCmsPageCategoryCommand
- DeleteCmsPageCategoryCommand
- EditCmsPageCategoryCommand
- ToggleCmsPageCategoryStatusCommand
### Queries
- GetCmsPageCategoriesForBreadcrumb
- GetCmsPageCategoryForEditing
- GetCmsPageCategoryNameForListing
- GetCmsPageParentCategoryIdForRedirection

## Configuration
### Commands
- SwitchDebugModeCommand

## Contact
### Commands
- AbstractContactCommand
- AddContactCommand
- EditContactCommand
### Queries
- GetContactForEditing

## Country
### Commands
- AddCountryCommand
- DeleteCountryCommand
- EditCountryCommand
### Queries
- GetCountryForEditing
- GetCountryRequiredFields

## CreditSlip
### Queries
- GetCreditSlipIdsByDateRange

## Currency
### Commands
- AbstractAddCurrencyCommand
- AbstractEditCurrencyCommand
- AddCurrencyCommand
- AddUnofficialCurrencyCommand
- BulkDeleteCurrenciesCommand
- BulkToggleCurrenciesStatusCommand
- DeleteCurrencyCommand
- EditCurrencyCommand
- EditUnofficialCurrencyCommand
- RefreshExchangeRatesCommand
- ToggleCurrencyStatusCommand
### Queries
- GetCurrencyExchangeRate
- GetCurrencyForEditing
- GetReferenceCurrency

## Customer
### Commands
- AddCustomerCommand
- BulkDeleteCustomerCommand
- BulkDisableCustomerCommand
- BulkEnableCustomerCommand
- DeleteCustomerCommand
- EditCustomerCommand
- SetPrivateNoteAboutCustomerCommand
- SetRequiredFieldsForCustomerCommand
- TransformGuestToCustomerCommand
- AddCustomerGroupCommand  [Group]
- DeleteCustomerGroupCommand  [Group]
- EditCustomerGroupCommand  [Group]
### Queries
- GetCustomerGroupForEditing  [Group]
- GetCustomerCarts
- GetCustomerForAddressCreation
- GetCustomerForEditing
- GetCustomerForViewing
- GetCustomerOrders
- GetRequiredFieldsForCustomer
- SearchCustomers

## CustomerMessage
### Commands
- AddOrderCustomerMessageCommand

## CustomerService
### Commands
- BulkDeleteCustomerThreadCommand
- DeleteCustomerThreadCommand
- ForwardCustomerThreadCommand
- ReplyToCustomerThreadCommand
- UpdateCustomerThreadStatusCommand
### Queries
- GetCustomerServiceSignature
- GetCustomerThreadForViewing

## Discount
### Commands
- AddDiscountCommand
- BulkDeleteDiscountsCommand
- BulkUpdateDiscountsStatusCommand
- DeleteDiscountCommand
- DuplicateDiscountCommand
- UpdateDiscountCommand
### Queries
- GetDiscountForEditing
- GetDiscountTypes

## Employee
### Commands
- AddEmployeeCommand
- BulkDeleteEmployeeCommand
- BulkUpdateEmployeeStatusCommand
- DeleteEmployeeCommand
- EditEmployeeCommand
- ResetEmployeePasswordCommand
- SendEmployeePasswordResetEmailCommand
- ToggleEmployeeStatusCommand
### Queries
- GetEmployeeEmailById
- GetEmployeeForEditing

## Feature
### Commands
- AddFeatureCommand
- AddFeatureValueCommand
- BulkDeleteFeatureCommand
- BulkDeleteFeatureValueCommand
- DeleteFeatureCommand
- DeleteFeatureValueCommand
- EditFeatureCommand
- EditFeatureValueCommand
### Queries
- GetFeatureForEditing
- GetFeatureValueForEditing

## Hook
### Commands
- UpdateHookStatusCommand
### Queries
- GetHook
- GetHookStatus

## ImageSettings
### Commands
- AddImageTypeCommand
- BulkDeleteImageTypeCommand
- DeleteImageTypeCommand
- DeleteImagesFromTypeCommand
- EditImageSettingsCommand
- EditImageTypeCommand
- RegenerateThumbnailsCommand
### Queries
- GetImageSettingsForEditing
- GetImageTypeForEditing

## Language
### Commands
- AddLanguageCommand
- BulkDeleteLanguagesCommand
- BulkToggleLanguagesStatusCommand
- DeleteLanguageCommand
- EditLanguageCommand
- ToggleLanguageStatusCommand
### Queries
- GetLanguageForEditing

## MailTemplate
### Commands
- GenerateThemeMailTemplatesCommand

## Manufacturer
### Commands
- AddManufacturerCommand
- BulkDeleteManufacturerCommand
- BulkToggleManufacturerStatusCommand
- DeleteManufacturerCommand
- DeleteManufacturerLogoImageCommand
- EditManufacturerCommand
- ToggleManufacturerStatusCommand
### Queries
- GetManufacturerForEditing
- GetManufacturerForViewing

## Meta
### Commands
- AbstractMetaCommand
- AddMetaCommand
- EditMetaCommand
### Queries
- GetMetaForEditing
- GetPagesForLayoutCustomization

## Module
### Commands
- BulkToggleModuleStatusCommand
- BulkUninstallModuleCommand
- InstallModuleCommand
- ResetModuleCommand
- UninstallModuleCommand
- UpdateModuleStatusCommand
- UpgradeModuleCommand
- UploadModuleCommand
### Queries
- GetModuleInfos

## Notification
### Commands
- UpdateEmployeeNotificationLastElementCommand
### Queries
- GetNotificationLastElements

## Order
### Commands
- AbstractRefundCommand
- AddCartRuleToOrderCommand
- AddOrderFromBackOfficeCommand
- BulkChangeOrderStatusCommand
- CancelOrderProductCommand
- ChangeOrderCurrencyCommand
- ChangeOrderDeliveryAddressCommand
- ChangeOrderInvoiceAddressCommand
- DeleteCartRuleFromOrderCommand
- DuplicateOrderCartCommand
- IssuePartialRefundCommand
- IssueReturnProductCommand
- IssueStandardRefundCommand
- ResendOrderEmailCommand
- SendProcessOrderEmailCommand
- SetInternalOrderNoteCommand
- UpdateOrderShippingDetailsCommand
- UpdateOrderStatusCommand
- GenerateInvoiceCommand  [Invoice]
- UpdateInvoiceNoteCommand  [Invoice]
- AddPaymentCommand  [Payment]
- AddProductToOrderCommand  [Product]
- DeleteProductFromOrderCommand  [Product]
- UpdateProductInOrderCommand  [Product]
### Queries
- GetOrderForViewing
- GetOrderPreview
- GetOrderProductsForViewing

## OrderMessage
### Commands
- AddOrderMessageCommand
- BulkDeleteOrderMessageCommand
- DeleteOrderMessageCommand
- EditOrderMessageCommand
### Queries
- GetOrderMessageForEditing

## OrderReturn
### Commands
- UpdateOrderReturnStateCommand
### Queries
- GetOrderReturnForEditing

## OrderReturnState
### Commands
- AddOrderReturnStateCommand
- BulkDeleteOrderReturnStateCommand
- DeleteOrderReturnStateCommand
- EditOrderReturnStateCommand
### Queries
- GetOrderReturnStateForEditing

## OrderState
### Commands
- AddOrderStateCommand
- BulkDeleteOrderStateCommand
- DeleteOrderStateCommand
- EditOrderStateCommand
### Queries
- GetOrderStateForEditing

## Product
### Commands
- RemoveAllAssociatedProductAttachmentsCommand  [Attachment]
- SetAssociatedProductAttachmentsCommand  [Attachment]
- BulkDeleteCombinationCommand  [Combination]
- DeleteCombinationCommand  [Combination]
- GenerateProductCombinationsCommand  [Combination]
- RemoveAllCombinationImagesCommand  [Combination]
- SetCombinationImagesCommand  [Combination]
- UpdateCombinationCommand  [Combination]
- UpdateCombinationStockAvailableCommand  [Combination]
- UpdateCombinationSuppliersCommand  [Combination]
- AddProductCommand
- AssignProductToCategoryCommand
- BulkDeleteProductCommand
- BulkDuplicateProductCommand
- BulkUpdateProductStatusCommand
- DeleteProductCommand
- DuplicateProductCommand
- RemoveAllAssociatedProductCategoriesCommand
- RemoveAllProductTagsCommand
- RemoveAllRelatedProductsCommand
- SetAssociatedProductCategoriesCommand
- SetCarriersCommand
- SetProductTagsCommand
- SetRelatedProductsCommand
- UpdateProductCommand
- UpdateProductTypeCommand
- UpdateProductsPositionsCommand
- RemoveAllCustomizationFieldsFromProductCommand  [Customization]
- SetProductCustomizationFieldsCommand  [Customization]
- RemoveAllFeatureValuesFromProductCommand  [FeatureValue]
- SetProductFeatureValuesCommand  [FeatureValue]
- AddProductImageCommand  [Image]
- DeleteProductImageCommand  [Image]
- ProductImageSetting  [Image]
- SetProductImagesForAllShopCommand  [Image]
- UpdateProductImageCommand  [Image]
- RemoveAllProductsFromPackCommand  [Pack]
- SetPackProductsCommand  [Pack]
- SetProductShopsCommand  [Shop]
- AddSpecificPriceCommand  [SpecificPrice]
- DeleteSpecificPriceCommand  [SpecificPrice]
- EditSpecificPriceCommand  [SpecificPrice]
- RemoveSpecificPricePriorityForProductCommand  [SpecificPrice]
- SetSpecificPricePriorityForProductCommand  [SpecificPrice]
- UpdateProductStockAvailableCommand  [Stock]
- RemoveAllAssociatedProductSuppliersCommand  [Supplier]
- SetProductDefaultSupplierCommand  [Supplier]
- SetSuppliersCommand  [Supplier]
- UpdateProductSuppliersCommand  [Supplier]
- AddVirtualProductFileCommand  [VirtualProductFile]
- DeleteVirtualProductFileCommand  [VirtualProductFile]
- UpdateVirtualProductFileCommand  [VirtualProductFile]
### Queries
- GetProductAttributeGroups  [AttributeGroup]
- GetCombinationForEditing  [Combination]
- GetCombinationIds  [Combination]
- GetCombinationSuppliers  [Combination]
- GetEditableCombinationsList  [Combination]
- SearchCombinationsForAssociation  [Combination]
- SearchProductCombinations  [Combination]
- GetProductCustomizationFields  [Customization]
- GetProductFeatureValues  [FeatureValue]
- GetProductImage  [Image]
- GetProductImages  [Image]
- GetShopProductImages  [Image]
- GetPackedProducts  [Pack]
- GetProductForEditing
- GetProductIsEnabled
- GetRelatedProducts
- SearchProducts
- SearchProductsForAssociation
- GetSpecificPriceForEditing  [SpecificPrice]
- GetSpecificPriceList  [SpecificPrice]
- GetCombinationStockMovements  [Stock]
- GetProductStockMovements  [Stock]
- GetAssociatedSuppliers  [Supplier]
- GetProductSupplierOptions  [Supplier]

## Profile
### Commands
- AbstractProfileCommand
- AddProfileCommand
- BulkDeleteProfileCommand
- DeleteProfileCommand
- EditProfileCommand
- UpdateModulePermissionsCommand  [Permission]
- UpdateTabPermissionsCommand  [Permission]
### Queries
- GetPermissionsForConfiguration  [Permission]
- GetProfileForEditing

## Search
### Commands
- SearchIndexationCommand

## SearchEngine
### Commands
- AddSearchEngineCommand
- BulkDeleteSearchEngineCommand
- DeleteSearchEngineCommand
- EditSearchEngineCommand
### Queries
- GetSearchEngineForEditing

## Security
### Commands
- BulkDeleteCustomerSessionsCommand
- BulkDeleteEmployeeSessionsCommand
- ClearOutdatedCustomerSessionCommand
- ClearOutdatedEmployeeSessionCommand
- DeleteCustomerSessionCommand
- DeleteEmployeeSessionCommand

## Shipment
### Commands
- AddProductToShipment
- CreateShipment
- DeleteProductFromShipment
- EditShipment
- MergeProductsToShipment
- SplitShipment
- SwitchShipmentCarrierCommand
### Queries
- GetOrderShipments
- GetShipmentForEditing
- GetShipmentForViewing
- GetShipmentProducts
- GetShipmentsForOrderDetail
- ListAvailableShipments
- ListAvailableShipmentsForProduct

## Shop
### Commands
- UploadLogosCommand
### Queries
- GetLogosPaths
- SearchShops

## ShowcaseCard
### Commands
- CloseShowcaseCardCommand
### Queries
- GetShowcaseCardIsClosed

## SqlManagement
### Commands
- AddSqlRequestCommand
- BulkDeleteSqlRequestCommand
- DeleteSqlRequestCommand
- EditSqlRequestCommand
- SaveSqlRequestSettingsCommand
### Queries
- GetDatabaseTableFieldsList
- GetDatabaseTablesList
- GetSqlRequestExecutionResult
- GetSqlRequestForEditing
- GetSqlRequestSettings

## State
### Commands
- AddStateCommand
- BulkDeleteStateCommand
- BulkToggleStateStatusCommand
- BulkUpdateStateZoneCommand
- DeleteStateCommand
- EditStateCommand
- ToggleStateStatusCommand
### Queries
- GetStateForEditing

## Store
### Commands
- BulkDeleteStoreCommand
- BulkUpdateStoreStatusCommand
- DeleteStoreCommand
- ToggleStoreStatusCommand
### Queries
- GetStoreForEditing

## Supplier
### Commands
- AbstractBulkSupplierCommand
- AddSupplierCommand
- BulkDeleteSupplierCommand
- BulkDisableSupplierCommand
- BulkEnableSupplierCommand
- DeleteSupplierCommand
- DeleteSupplierLogoImageCommand
- EditSupplierCommand
- ToggleSupplierStatusCommand
### Queries
- GetSupplierForEditing
- GetSupplierForViewing

## Tab
### Commands
- UpdateTabStatusByClassNameCommand

## Tag
### Commands
- AddTagCommand
- BulkDeleteTagCommand
- DeleteTagCommand
- EditTagCommand
### Queries
- GetTagForEditing

## Tax
### Commands
- AddTaxCommand
- BulkDeleteTaxCommand
- BulkToggleTaxStatusCommand
- DeleteTaxCommand
- EditTaxCommand
- ToggleTaxStatusCommand
### Queries
- GetTaxForEditing

## TaxRulesGroup
### Commands
- AddTaxRulesGroupCommand
- BulkDeleteTaxRulesGroupCommand
- BulkSetTaxRulesGroupStatusCommand
- DeleteTaxRulesGroupCommand
- EditTaxRulesGroupCommand
- SetTaxRulesGroupStatusCommand
### Queries
- GetTaxRulesGroupForEditing

## Theme
### Commands
- AdaptThemeToRTLLanguagesCommand
- DeleteThemeCommand
- EnableThemeCommand
- ImportThemeCommand
- ResetThemeLayoutsCommand

## Title
### Commands
- AddTitleCommand
- BulkDeleteTitleCommand
- DeleteTitleCommand
- EditTitleCommand
### Queries
- GetTitleForEditing

## Webservice
### Commands
- AddWebserviceKeyCommand
- BulkDeleteWebserviceKeyCommand
- DeleteWebserviceKeyCommand
- EditWebserviceKeyCommand
### Queries
- GetWebserviceKeyForEditing

## Zone
### Commands
- AddZoneCommand
- BulkDeleteZoneCommand
- BulkToggleZoneStatusCommand
- DeleteZoneCommand
- EditZoneCommand
- ToggleZoneStatusCommand
### Queries
- GetZoneForEditing

