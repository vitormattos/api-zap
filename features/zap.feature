Feature: Zap API

  Scenario: Populate
    Given I send "GET" to "/api/zap-search" using rows (200)
      | categoryPage | RESULT |
      | business | RENTAL |
      | listingType | USED |
      | portal | ZAP |
      | unitTypes | APARTMENT,APARTMENT,APARTMENT,HOME,HOME,HOME,APARTMENT,APARTMENT,APARTMENT,ALLOTMENT_LAND,FARM |
      | unitSubTypes | UnitSubType_NONE,DUPLEX,TRIPLEX%7CSTUDIO%7CKITNET%7CUnitSubType_NONE,TWO_STORY_HOUSE,SINGLE_STOREY_HOUSE,KITNET%7CCONDOM INIUM%7CVILLAGE_HOUSE%7CPENTHOUSE%7CFLAT%7CLOFT%7CUnitSubType_NONE,CONDOMINIUM,VILLAGE_HOUSE%7CUnitSubType_NONE,CONDOMINIUM|
      | usageTypes | RESIDENTIAL,RESIDENTIAL,RESIDENTIAL,RESIDENTIAL,RESIDENTIAL,RESIDENTIAL,RESIDENTIAL,RESIDENTIAL,RESIDENTIAL,RESIDE NTIAL,RESIDENTIAL|
      | unitTypesV3 | APARTMENT,UnitType_NONE,KITNET,HOME,CONDOMINIUM,VILLAGE_HOUSE,PENTHOUSE,FLAT,LOFT,RESIDENTIAL_ALLOTMENT_LAND,FA RM|
      | addressCity | Rio de Janeiro |
      | addressState | Rio de Janeiro |
      | size | 100 |
      | from | 0 |
      | page | 1 |
      | includeFields | search(result(listings(listing (listingsCount,sourceId,displayAddressType,amenities,usableAreas,constructionStatus,listingType,description,title,stamps,createdAt,floors,unitTypes,nonActivationReason,providerId,propertyType,unitSubTypes,unitsOnTheFloor,legacyId,id,portal,unitFloor,parkingSpaces,updatedAt,address,suites,publicationType,externalId,bathrooms,usageTypes,totalAreas,advertiserId,advertiserContact,whatsappNumber,bedrooms,acceptExchange,pricingInfos,showPrice,resale,buildings,capacityLimit,status,priceSuggestion),account(id,name,logoUrl,licenseNumber,showAddress,legacyVivarealId,legacyZapId,createdDate,minisite),medias,accountLink,link)),totalCount),page,facets,fullUriFragments,superPremium(search(result(listings(listing(listingsCount,sourceId,displayAddressType,amenities,usableAreas,constructionStatus,listingType,description,title,stamps,createdAt,floors,unitTypes,nonActivationReason,providerId,propertyType,unitSubTypes,unitsOnTheFloor,legacyId,id,portal,unitFloor,parkingSpaces,updatedAt,address,suites,publicationType,externalId,bathrooms,usageTypes,totalAreas,advertiserId,advertiserContact,whatsappNumber,bedrooms,acceptExchange,pricingInfos,showPrice,resale,buildings,capacityLimit,status,priceSuggestion),account(id,name,logoUrl,licenseNumber,showAddress,legacyVivarealId,legacyZapId,createdDate,minisite),medias,accountLink,link)),totalCount)),premiere(search(result(listings(listing(listingsCount,sourceId,displayAddressType,amenities,usableAreas,constructionStatus,listingType,description,title,stamps,createdAt,floors,unitTypes,nonActivationReason,providerId,propertyType,unitSubTypes,unitsOnTheFloor,legacyId,id,portal,unitFloor,parkingSpaces,updatedAt,address,suites,publicationType,externalId,bathrooms,usageTypes,totalAreas,advertiserId,advertiserContact,whatsappNumber,bedrooms,acceptExchange,pricingInfos,showPrice,resale,buildings,capacityLimit,status,priceSuggestion),account(id,name,logoUrl,licenseNumber,showAddress,legacyVivarealId,legacyZapId,createdDate,minisite),medias,accountLink,link)),totalCount))|
      | developmentsSize | 3 |
      | superPremiumSize | 0 |
      | levels | CITY |
      | ref |  |
      | __zt | mtc:deduplication |
      | addressType | city |
