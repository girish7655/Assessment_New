import * as yup from 'yup';

export const authorRules = yup.object().shape({
    name: yup.string()
        .required('Name is required')
        .min(2, 'Name must be at least 2 characters')
        .max(255, 'Name must not exceed 255 characters'),
    birth_date: yup.mixed()
        .nullable()
        .transform((value) => {
            if (!value) return null;
            const date = new Date(value);
            return isNaN(date) ? undefined : date;
        })
        .test('is-valid-date', 'Please enter a valid date', function(value) {
            if (!value) return true; 
            return value instanceof Date && !isNaN(value);
        })
        .test('is-before-today', 'Birth date must be before today', function(value) {
            if (!value) return true; 
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            return value < today;
        }),
    nationality: yup.string()
        .nullable()
        .max(100, 'Nationality must not exceed 100 characters'),
});
