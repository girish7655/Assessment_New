import * as yup from 'yup';

const SUPPORTED_FORMATS = ['image/jpg', 'image/jpeg', 'image/gif', 'image/png'];
const FILE_SIZE = 2 * 1024 * 1024; // 2MB

export const bookRules = yup.object().shape({
    title: yup.string()
        .required('Title is required')
        .max(255, 'Title must not exceed 255 characters'),
    author_id: yup.number()
        .required('Author is required')
        .nullable()
        .transform((value) => (isNaN(value) ? null : value))
        .positive('Please select an author'),
    publisher_id: yup.number()
        .required('Publisher is required')
        .nullable()
        .transform((value) => (isNaN(value) ? null : value))
        .positive('Please select a publisher'),
    category_id: yup.number()
        .required('Category is required')
        .nullable()
        .transform((value) => (isNaN(value) ? null : value))
        .positive('Please select a category'),
    isbn: yup.string()
        .required('ISBN is required')
        .matches(/^[\d-]+$/, 'ISBN must contain only numbers and hyphens'),
    published_year: yup.mixed()
        .required('Published year is required')
        .transform((value) => {
            if (!value) return null;
            const num = Number(value);
            return isNaN(num) ? undefined : num;
        })
        .test('is-number', 'Published year must be a valid number', value => 
            value === null || (typeof value === 'number' && !isNaN(value)))
        .test('min', 'Published year must be after 1000', value => 
            value === null || value >= 1000)
        .test('max', 'Published year cannot be in the future', value => 
            value === null || value <= new Date().getFullYear() + 1),
    description: yup.string()
        .required('Description is required'),
});
