<?php
abstract class AbstractAssetManager extends AbstractAssetManagerCore {};
abstract class AbstractCheckoutStep extends AbstractCheckoutStepCore {};
abstract class AbstractForm extends AbstractFormCore {};
abstract class AbstractLogger extends AbstractLoggerCore {};
class Access extends AccessCore {};
class Address extends AddressCore {};
class AddressChecksum extends AddressChecksumCore {};
class AddressFormat extends AddressFormatCore {};
class AddressValidator extends AddressValidatorCore {};
class AdminController extends AdminControllerCore {};
class Alias extends AliasCore {};
class Attachment extends AttachmentCore {};
class AttributeGroup extends AttributeGroupCore {};
class AttributeGroupLang extends AttributeGroupLangCore {};
class AttributeLang extends AttributeLangCore {};
class CMS extends CMSCore {};
class CMSCategory extends CMSCategoryCore {};
class CMSRole extends CMSRoleCore {};
class CSV extends CSVCore {};
abstract class Cache extends CacheCore {};
class CacheApc extends CacheApcCore {};
class CacheMemcache extends CacheMemcacheCore {};
class CacheMemcached extends CacheMemcachedCore {};
class CacheXcache extends CacheXcacheCore {};
class Carrier extends CarrierCore {};
class CarrierLang extends CarrierLangCore {};
abstract class CarrierModule extends CarrierModuleCore {};
class Cart extends CartCore {};
class CartChecksum extends CartChecksumCore {};
class CartRule extends CartRuleCore {};
class Category extends CategoryCore {};
class CategoryLang extends CategoryLangCore {};
class CccReducer extends CccReducerCore {};
class Chart extends ChartCore {};
class CheckoutAddressesStep extends CheckoutAddressesStepCore {};
class CheckoutDeliveryStep extends CheckoutDeliveryStepCore {};
class CheckoutPaymentStep extends CheckoutPaymentStepCore {};
class CheckoutPersonalInformationStep extends CheckoutPersonalInformationStepCore {};
class CheckoutProcess extends CheckoutProcessCore {};
class CheckoutSession extends CheckoutSessionCore {};
class CmsCategoryLang extends CmsCategoryLangCore {};
class Combination extends CombinationCore {};
class ConditionsToApproveFinder extends ConditionsToApproveFinderCore {};
class Configuration extends ConfigurationCore {};
class ConfigurationKPI extends ConfigurationKPICore {};
class ConfigurationLang extends ConfigurationLangCore {};
class ConfigurationTest extends ConfigurationTestCore {};
class Connection extends ConnectionCore {};
class ConnectionsSource extends ConnectionsSourceCore {};
class Contact extends ContactCore {};
class ContactLang extends ContactLangCore {};
class Context extends ContextCore {};
abstract class Controller extends ControllerCore {};
class Cookie extends CookieCore {};
class Country extends CountryCore {};
class CssMinifier extends CssMinifierCore {};
class Currency extends CurrencyCore {};
class Curve extends CurveCore {};
class Customer extends CustomerCore {};
class CustomerAddress extends CustomerAddressCore {};
class CustomerAddressForm extends CustomerAddressFormCore {};
class CustomerAddressFormatter extends CustomerAddressFormatterCore {};
class CustomerAddressPersister extends CustomerAddressPersisterCore {};
class CustomerForm extends CustomerFormCore {};
class CustomerFormatter extends CustomerFormatterCore {};
class CustomerLoginForm extends CustomerLoginFormCore {};
class CustomerLoginFormatter extends CustomerLoginFormatterCore {};
class CustomerMessage extends CustomerMessageCore {};
class CustomerPersister extends CustomerPersisterCore {};
class CustomerSession extends CustomerSessionCore {};
class CustomerThread extends CustomerThreadCore {};
class Customization extends CustomizationCore {};
class CustomizationField extends CustomizationFieldCore {};
class DataLang extends DataLangCore {};
class DateRange extends DateRangeCore {};
abstract class Db extends DbCore {};
class DbMySQLi extends DbMySQLiCore {};
class DbPDO extends DbPDOCore {};
class DbQuery extends DbQueryCore {};
class Delivery extends DeliveryCore {};
class DeliveryOptionsFinder extends DeliveryOptionsFinderCore {};
class Dispatcher extends DispatcherCore {};
class Employee extends EmployeeCore {};
class EmployeeSession extends EmployeeSessionCore {};
class Feature extends FeatureCore {};
class FeatureFlag extends FeatureFlagCore {};
class FeatureLang extends FeatureLangCore {};
class FeatureValue extends FeatureValueCore {};
class FeatureValueLang extends FeatureValueLangCore {};
class FileLogger extends FileLoggerCore {};
class FileUploader extends FileUploaderCore {};
class FormField extends FormFieldCore {};
class FrontController extends FrontControllerCore {};
class Gender extends GenderCore {};
class GenderLang extends GenderLangCore {};
class Group extends GroupCore {};
class GroupLang extends GroupLangCore {};
class GroupReduction extends GroupReductionCore {};
class Guest extends GuestCore {};
abstract class HTMLTemplate extends HTMLTemplateCore {};
class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore {};
class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore {};
class HTMLTemplateOrderReturn extends HTMLTemplateOrderReturnCore {};
class HTMLTemplateOrderSlip extends HTMLTemplateOrderSlipCore {};
class HTMLTemplateSupplyOrderForm extends HTMLTemplateSupplyOrderFormCore {};
class Helper extends HelperCore {};
class HelperCalendar extends HelperCalendarCore {};
class HelperForm extends HelperFormCore {};
class HelperImageUploader extends HelperImageUploaderCore {};
class HelperKpi extends HelperKpiCore {};
class HelperKpiRow extends HelperKpiRowCore {};
class HelperList extends HelperListCore {};
class HelperOptions extends HelperOptionsCore {};
class HelperShop extends HelperShopCore {};
class HelperTreeCategories extends HelperTreeCategoriesCore {};
class HelperTreeShops extends HelperTreeShopsCore {};
class HelperUploader extends HelperUploaderCore {};
class HelperView extends HelperViewCore {};
class Hook extends HookCore {};
class Image extends ImageCore {};
class ImageManager extends ImageManagerCore {};
class ImageType extends ImageTypeCore {};
class JavascriptManager extends JavascriptManagerCore {};
class JsMinifier extends JsMinifierCore {};
class Language extends LanguageCore {};
class Link extends LinkCore {};
class LinkProxy extends LinkProxyCore {};
class LocalizationPack extends LocalizationPackCore {};
class Mail extends MailCore {};
class Manufacturer extends ManufacturerCore {};
class ManufacturerAddress extends ManufacturerAddressCore {};
class Media extends MediaCore {};
class Message extends MessageCore {};
class Meta extends MetaCore {};
class MetaLang extends MetaLangCore {};
abstract    class Module extends ModuleCore {};
abstract class ModuleAdminController extends ModuleAdminControllerCore {};
class ModuleFrontController extends ModuleFrontControllerCore {};
abstract class ModuleGraph extends ModuleGraphCore {};
abstract class ModuleGraphEngine extends ModuleGraphEngineCore {};
abstract class ModuleGrid extends ModuleGridCore {};
abstract class ModuleGridEngine extends ModuleGridEngineCore {};
class Notification extends NotificationCore {};
abstract class ObjectModel extends ObjectModelCore {};
class Order extends OrderCore {};
class OrderCarrier extends OrderCarrierCore {};
class OrderCartRule extends OrderCartRuleCore {};
class OrderDetail extends OrderDetailCore {};
class OrderHistory extends OrderHistoryCore {};
class OrderInvoice extends OrderInvoiceCore {};
class OrderMessage extends OrderMessageCore {};
class OrderMessageLang extends OrderMessageLangCore {};
class OrderPayment extends OrderPaymentCore {};
class OrderReturn extends OrderReturnCore {};
class OrderReturnState extends OrderReturnStateCore {};
class OrderReturnStateLang extends OrderReturnStateLangCore {};
class OrderSlip extends OrderSlipCore {};
class OrderState extends OrderStateCore {};
class OrderStateLang extends OrderStateLangCore {};
class PDF extends PDFCore {};
class PDFGenerator extends PDFGeneratorCore {};
class Pack extends PackCore {};
class Page extends PageCore {};
abstract class PaymentModule extends PaymentModuleCore {};
class PaymentOptionsFinder extends PaymentOptionsFinderCore {};
class PhpEncryption extends PhpEncryptionCore {};
class PhpEncryptionEngine extends PhpEncryptionEngineCore {};
class PrestaShopBackup extends PrestaShopBackupCore {};
class PrestaShopCollection extends PrestaShopCollectionCore {};
class PrestaShopDatabaseException extends PrestaShopDatabaseExceptionCore {};
class PrestaShopException extends PrestaShopExceptionCore {};
class PrestaShopLogger extends PrestaShopLoggerCore {};
class PrestaShopModuleException extends PrestaShopModuleExceptionCore {};
class PrestaShopObjectNotFoundException extends PrestaShopObjectNotFoundExceptionCore {};
class PrestaShopPaymentException extends PrestaShopPaymentExceptionCore {};
class Product extends ProductCore {};
class ProductAssembler extends ProductAssemblerCore {};
class ProductAttribute extends ProductAttributeCore {};
class ProductDownload extends ProductDownloadCore {};
abstract class ProductListingFrontController extends ProductListingFrontControllerCore {};
class ProductPresenterFactory extends ProductPresenterFactoryCore {};
abstract class ProductPresentingFrontController extends ProductPresentingFrontControllerCore {};
class ProductSale extends ProductSaleCore {};
class ProductSupplier extends ProductSupplierCore {};
class Profile extends ProfileCore {};
class ProfileLang extends ProfileLangCore {};
class QqUploadedFileForm extends QqUploadedFileFormCore {};
class QqUploadedFileXhr extends QqUploadedFileXhrCore {};
class QuickAccess extends QuickAccessCore {};
class QuickAccessLang extends QuickAccessLangCore {};
class RangePrice extends RangePriceCore {};
class RangeWeight extends RangeWeightCore {};
class RequestSql extends RequestSqlCore {};
class Risk extends RiskCore {};
class RiskLang extends RiskLangCore {};
class Search extends SearchCore {};
class SearchEngine extends SearchEngineCore {};
class Shop extends ShopCore {};
class ShopGroup extends ShopGroupCore {};
class ShopUrl extends ShopUrlCore {};
class SmartyCustom extends SmartyCustomCore {};
class SmartyCustomTemplate extends SmartyCustomTemplateCore {};
class SmartyDevTemplate extends SmartyDevTemplateCore {};
class SmartyResourceModule extends SmartyResourceModuleCore {};
class SmartyResourceParent extends SmartyResourceParentCore {};
class SpecificPrice extends SpecificPriceCore {};
class SpecificPriceFormatter extends SpecificPriceFormatterCore {};
class SpecificPriceRule extends SpecificPriceRuleCore {};
class State extends StateCore {};
class Stock extends StockCore {};
class StockAvailable extends StockAvailableCore {};
class StockManager extends StockManagerCore {};
class StockManagerFactory extends StockManagerFactoryCore {};
abstract class StockManagerModule extends StockManagerModuleCore {};
class StockMvt extends StockMvtCore {};
class StockMvtReason extends StockMvtReasonCore {};
class StockMvtReasonLang extends StockMvtReasonLangCore {};
class StockMvtWS extends StockMvtWSCore {};
class Store extends StoreCore {};
class StylesheetManager extends StylesheetManagerCore {};
class Supplier extends SupplierCore {};
class SupplierAddress extends SupplierAddressCore {};
class SupplyOrder extends SupplyOrderCore {};
class SupplyOrderDetail extends SupplyOrderDetailCore {};
class SupplyOrderHistory extends SupplyOrderHistoryCore {};
class SupplyOrderReceiptHistory extends SupplyOrderReceiptHistoryCore {};
class SupplyOrderState extends SupplyOrderStateCore {};
class SupplyOrderStateLang extends SupplyOrderStateLangCore {};
class Tab extends TabCore {};
class TabLang extends TabLangCore {};
class Tag extends TagCore {};
class Tax extends TaxCore {};
class TaxCalculator extends TaxCalculatorCore {};
class TaxConfiguration extends TaxConfigurationCore {};
class TaxManagerFactory extends TaxManagerFactoryCore {};
abstract class TaxManagerModule extends TaxManagerModuleCore {};
class TaxRule extends TaxRuleCore {};
class TaxRulesGroup extends TaxRulesGroupCore {};
class TaxRulesTaxManager extends TaxRulesTaxManagerCore {};
class TemplateFinder extends TemplateFinderCore {};
class ThemeLang extends ThemeLangCore {};
class Tools extends ToolsCore {};
class Translate extends TranslateCore {};
class TranslatedConfiguration extends TranslatedConfigurationCore {};
class Tree extends TreeCore {};
class TreeToolbar extends TreeToolbarCore {};
abstract class TreeToolbarButton extends TreeToolbarButtonCore {};
class TreeToolbarLink extends TreeToolbarLinkCore {};
class TreeToolbarSearch extends TreeToolbarSearchCore {};
class TreeToolbarSearchCategories extends TreeToolbarSearchCategoriesCore {};
class Upgrader extends UpgraderCore {};
class Uploader extends UploaderCore {};
class Validate extends ValidateCore {};
class ValidateConstraintTranslator extends ValidateConstraintTranslatorCore {};
class Warehouse extends WarehouseCore {};
class WarehouseAddress extends WarehouseAddressCore {};
class WarehouseProductLocation extends WarehouseProductLocationCore {};
class WebserviceException extends WebserviceExceptionCore {};
class WebserviceKey extends WebserviceKeyCore {};
class WebserviceOutputBuilder extends WebserviceOutputBuilderCore {};
class WebserviceOutputJSON extends WebserviceOutputJSONCore {};
class WebserviceOutputXML extends WebserviceOutputXMLCore {};
class WebserviceRequest extends WebserviceRequestCore {};
class WebserviceSpecificManagementAttachments extends WebserviceSpecificManagementAttachmentsCore {};
class WebserviceSpecificManagementImages extends WebserviceSpecificManagementImagesCore {};
class WebserviceSpecificManagementSearch extends WebserviceSpecificManagementSearchCore {};
class Zone extends ZoneCore {};
