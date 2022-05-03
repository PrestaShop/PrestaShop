
export function notifyFormErrors(jsonResponse: any): void {
  Object.keys(jsonResponse.errors).forEach((field: string) => {
    if (Object.prototype.hasOwnProperty.call(jsonResponse.errors, field)) {
      const fieldErrors: string[] = jsonResponse.errors[field];
      const errors: string = fieldErrors.join(' ');
      $.growl.error({message: `${field}: ${errors}`});
    }
  });
};

export default {
  notifyFormErrors,
};
