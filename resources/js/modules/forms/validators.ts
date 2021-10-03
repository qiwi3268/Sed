import { helpers, maxLength, maxValue, minValue, required, requiredIf } from '@vuelidate/validators';
import { Ref } from '@vue/reactivity';
import { ErrorObject, ValidationRuleWithoutParams, ValidationRuleWithParams } from '@vuelidate/core';
import { add, isAfter, isFuture } from 'date-fns';
import { formatDate } from '@/modules/forms/formatting';
import isNumber from 'lodash/isNumber';
import isDate from 'lodash/isDate';

export type FormUnit<T = unknown> = {
   $model: T
   // const validationGetters
   readonly $dirty: boolean
   readonly $error: boolean
   readonly $errors: ErrorObject[]
   readonly $silentErrors: ErrorObject[]
   readonly $invalid: boolean
   readonly $anyDirty: boolean
   readonly $pending: boolean
   readonly $path: string

   // const validationMethods
   readonly $touch: () => void
   readonly $reset: () => void
   readonly $validate: () => Promise<boolean>

   // const validationRules
   readonly required: ValidationRuleWithoutParams
   readonly mRequired: ValidationRuleWithoutParams

   // const cache
   modelBeforeUnmount?: T
}

export type ValidationConfig = {
   [fieldName: string]: {
      [ruleName: string]: ValidationRuleWithoutParams | ValidationRuleWithParams
   }
}

export enum DateInterval {
   Future = 1,
   Past = -1
}

export type DateOffset = {
   years?: number
   months?: number
   weeks?: number
   days?: number
   hours?: number
   minutes?: number
   seconds?: number
}

export const mRequiredIf = (displayed: boolean): ValidationRuleWithParams => helpers.withMessage('Поле обязательно для заполнения', requiredIf(displayed));

export const mRequired = helpers.withMessage('Поле обязательно для заполнения', required);

export const number = helpers.withMessage('Значение должно быть числом', (value) => !helpers.req(value) || isNumber(value));

const minValValidatorFn = (value) => !helpers.req(value) || (isNumber(value) && value > 0);
export const minVal = (min: number): ValidationRuleWithParams => helpers.withMessage(`Минимальное значение: ${min}`, minValValidatorFn);
export const positive = minVal(1);

export const unique = helpers.withMessage(
   'Поле должно содержать массив уникальных значений',
   (array: unknown) => Array.isArray(array) && (new Set(array)).size === array.length
);

export const max = (length: number | Ref<number>): ValidationRuleWithParams => helpers.withMessage(`Максимальная длина значения: ${length} символов`, maxLength(length));
export const inputMaxLength = max(200);
export const textMaxLength = max(400);

export const maxVal = (max: number | Ref<number>): ValidationRuleWithParams => helpers.withMessage(`Максимальное значение поля: ${max}`, maxValue(max));


export const date = helpers.withMessage('Некорректная дата', (date: Date) => !helpers.req(date) || isDate(date));

export const notEarlier = (offset: DateOffset, message?: string): ValidationRuleWithParams => {
   const targetDate = add(new Date(), offset);
   const errorMessage = message ?? `Дата должна быть не ранее ${formatDate(targetDate)}`;

   return helpers.withMessage(errorMessage, (date) => !helpers.req(date) || isAfter(date as Date, add(new Date(), offset)));
};

export const future = helpers.withMessage('Дата должна быть будущей', (date: Date) => !helpers.req(date) || (isDate(date) && isFuture(date)));

export const isAbsoluteUrl = (value: string): boolean => !helpers.req(value) || value.indexOf('http://') === 0 || value.indexOf('https://') === 0;
export const absoluteUrl = helpers.withMessage('Значение должно быть ссылкой', isAbsoluteUrl);

