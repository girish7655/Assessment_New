import * as yup from 'yup';

export const publisherRules = yup.object().shape({
    name: yup.string()
        .required('Name is required')
        .min(2, 'Name must be at least 2 characters')
        .max(255, 'Name must not exceed 255 characters'),
    address: yup.string()
        .nullable()
        .max(255, 'Address must not exceed 255 characters'),
    phone: yup.string()
        .nullable()
        .matches(/^([0-9\s\-\+\(\)]*)$/, 'Invalid phone number format')
        .max(20, 'Phone number must not exceed 20 characters')
        .transform(value => value?.trim() || null)
});