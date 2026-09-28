# Doctrine Entities Index (generated 2026-10-02)
# 28 entities in src/PrestaShopBundle/Entity/
#
# Columns: scalar DB-mapped fields. Relations: association targets.

## AdminFilter
  columns: id employee shop controller action filter filterId 

## ApiClient
  columns: id clientId clientName clientSecret enabled scopes description externalIssuer lifetime 

## Attribute
  columns: id color position 
  relations: ManyToOne→AttributeGroup ManyToMany→Shop OneToMany→AttributeLang 

## AttributeGroup
  columns: id isColorGroup groupType position 
  relations: OneToMany→Attribute ManyToMany→Shop OneToMany→AttributeGroupLang 

## AttributeGroupLang
  columns: name publicName 
  relations: ManyToOne→AttributeGroup ManyToOne→Lang 

## AttributeLang
  columns: name 
  relations: ManyToOne→Attribute ManyToOne→Lang 

## CspLog
  columns: id shopId directive source documentUri hits dateAdd dateUpd 

## CspRule
  columns: id shopId directive source dateAdd 

## FeatureFlag
  columns: id name type state labelWording labelDomain descriptionWording descriptionDomain stability 

## ImageType
  columns: id name width height imageFitment products categories manufacturers suppliers stores 

## ImportJob
  columns: uuid entityType shopId status fileName skipRows dataRecordCount currentPhaseId currentOffset resumeCursor skippedRowCount phaseTotals skippedRows messages context options dateAdd dateUpd 

## Lang
  columns: id name active isoCode languageCode locale dateFormatLite dateFormatFull isRtl 
  relations: OneToMany→Translation ManyToMany→Shop 

## ModuleHistory
  columns: id idEmployee idModule dateAdd dateUpd 

## Mutation
  columns: id mutationTable mutationRowId action mutatorType mutatorIdentifier mutationDetails dateAdd 

## ProductDownload
  columns: id idProduct displayFilename filename dateAdd dateExpiration nbDaysAccessible nbDownloadable active isShareable 

## ProductIdentity

## Shipment
  columns: id orderId carrierId addressId shippingCostTaxExcluded shippingCostTaxIncluded packedAt shippedAt deliveredAt cancelledAt trackingNumber deleted createdAt updatedAt 
  relations: OneToMany→ShipmentProduct 

## ShipmentProduct
  columns: id orderDetailId quantity 
  relations: ManyToOne→Shipment 

## Shop
  columns: id name color idCategory themeName active deleted 
  relations: ManyToOne→ShopGroup OneToMany→ShopUrl 

## ShopGroup
  columns: id name color shareCustomer shareOrder shareStock active deleted 
  relations: OneToMany→Shop 

## ShopUrl
  columns: id domain domainSsl physicalUri virtualUri main active 
  relations: ManyToOne→Shop 

## StockMvt
  columns: idStockMvt idStock idOrder idSupplyOrder idStockMvtReason idEmployee employeeLastname employeeFirstname physicalQuantity dateAdd sign priceTe lastWa currentWa referer 

## Tab
  columns: id idParent position module className routeName active enabled icon wording wordingDomain 
  relations: OneToMany→TabLang 

## TabLang
  columns: name 
  relations: ManyToOne→Tab ManyToOne→Lang 

## Translation
  columns: id key translation domain theme 
  relations: ManyToOne→Lang 

