<?xml version="1.0" ?>
<AccessRequest xml:lang="en-US">
	<AccessLicenseNumber>[[AccessLicenseNumber]]</AccessLicenseNumber>
	<UserId>[[UserId]]</UserId>
	<Password>[[Password]]</Password>
</AccessRequest>
<RatingServiceSelectionRequest>
	<Request>
		<TransactionReference>
			<CustomerContext>Rating and Service for Prestashop</CustomerContext>
		</TransactionReference>
		<RequestAction>Rate</RequestAction>
		<RequestOption>Rate</RequestOption>
	</Request>
	<PickupType>
		<Code>[[PickupTypeCode]]</Code>
		<Description>Pickup Description</Description>
	</PickupType>
	<Shipment>
		<Description>Rate Shopping - Domestic</Description>
		<Shipper>
			<ShipperNumber>[[ShipperNumber]]</ShipperNumber>
			<Address>
				<AddressLine1>[[ShipperAddressLine1]]</AddressLine1>
				<AddressLine2>[[ShipperAddressLine2]]</AddressLine2>
				<AddressLine3 />
				<City>[[ShipperCity]]</City>
				<PostalCode>[[ShipperPostalCode]]</PostalCode>
				<CountryCode>[[ShipperCountryCode]]</CountryCode>
				<StateProvinceCode>[[ShipperStateCode]]</StateProvinceCode>
			</Address>
		</Shipper>
		<ShipTo>
			<Address>
				<AddressLine1>[[ShipToAddressLine1]]</AddressLine1>
				<AddressLine2>[[ShipToAddressLine2]]</AddressLine2>
				<AddressLine3 />
				<City>[[ShipToCity]]</City>
				<PostalCode>[[ShipToPostalCode]]</PostalCode>
				<CountryCode>[[ShipToCountryCode]]</CountryCode>
				<StateProvinceCode>[[ShipToStateCode]]</StateProvinceCode>
			</Address>
		</ShipTo>
		<ShipFrom>
			<Address>
				<AddressLine1>[[ShipFromAddressLine1]]</AddressLine1>
				<AddressLine2>[[ShipFromAddressLine2]]</AddressLine2>
				<AddressLine3 />
				<City>[[ShipFromCity]]</City>
				<PostalCode>[[ShipFromPostalCode]]</PostalCode>
				<CountryCode>[[ShipFromCountryCode]]</CountryCode>
				<StateProvinceCode>[[ShipFromStateCode]]</StateProvinceCode>
			</Address>
		</ShipFrom>
		<Service><Code>[[Service]]</Code></Service>
		[[PackageList]]
		<ShipmentServiceOptions />
	</Shipment>
</RatingServiceSelectionRequest>
