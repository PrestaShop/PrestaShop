type CustomerCreator = {
  id?: number
  socialTitle?: string
  firstName?: string
  lastName?: string
  birthdate?: string
  yearOfBirth?: string
  monthOfBirth?: string
  dayOfBirth?: string
  email?: string
  password?: string
  enabled?: boolean
  newsletter?: boolean
  partnerOffers?: boolean
  defaultCustomerGroup?: string
  company?: string
  allowedOutstandingAmount?: number
  riskRating?: string
};

export default CustomerCreator;
