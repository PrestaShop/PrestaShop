type OrderStatusCreator = {
  id?: number
  name?: string
  color?: string
  logableOn?: boolean
  invoiceOn?: boolean
  hiddenOn?: boolean
  sendEmailOn?: boolean
  pdfInvoiceOn?: boolean
  pdfDeliveryOn?: boolean
  shippedOn?: boolean
  paidOn?: boolean
  deliveryOn?: boolean
  emailTemplate?: string
};

export default OrderStatusCreator;
