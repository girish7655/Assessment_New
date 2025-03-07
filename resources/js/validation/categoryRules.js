import * as yup from 'yup';

export const categoryRules = yup.object().shape({
    name: yup.string()
        .required('Name is required')
        .min(2, 'Name must be at least 2 characters')
        .max(255, 'Name must not exceed 255 characters'),
    description: yup.string()
        .nullable()
        .max(1000, 'Description must not exceed 1000 characters'),
    parent_id: yup.number()
        .nullable()
        .transform(value => (isNaN(value) ? null : value)),
    display_order: yup.number()
        .nullable()
        .min(0, 'Display order must be a positive number')
        .transform(value => (isNaN(value) ? null : value)),
});