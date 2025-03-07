import { configure } from 'vee-validate';
import * as yup from 'yup';

const passwordRules = yup.string()
    .required('Password is required')
    .min(8, 'Password must be at least 8 characters')
    .matches(/[A-Z]/, 'Password must contain at least one uppercase letter')
    .matches(/[a-z]/, 'Password must contain at least one lowercase letter')
    .matches(/[0-9]/, 'Password must contain at least one number')
    .matches(/[!@#$%^&*(),.?":{}|<>]/, 'Password must contain at least one special character');

export const registerRules = yup.object().shape({
    name: yup.string()
        .required('Name is required')
        .min(2, 'Name must be at least 2 characters'),
    email: yup.string()
        .required('Email is required')
        .email('Please enter a valid email'),
    password: passwordRules,
    password_confirmation: yup.string()
        .required('Please confirm your password')
        .oneOf([yup.ref('password')], 'Passwords must match'),
    role_id: yup.string()
        .required('Please select a role')
});

configure({
    validateOnBlur: true,
    validateOnChange: true,
    validateOnInput: true
});
