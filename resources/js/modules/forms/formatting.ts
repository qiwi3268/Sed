import { MiscItem } from '@/store/modules/misc';
import { GeFile } from '@/store/modules/modals/files/uploader';
import { parseISO, roundToNearestMinutes, format, isDate } from 'date-fns';
import { LogicError } from '@/modules/errors/LogicError';
import { getInflectedMonthNameByNumber } from '@/modules/utils/calendar';
import { ViewPagination } from '@/types/api';

export type ModelValue = string | string[] | number | number[] | MiscItem | MiscItem[] | boolean | null | GeFile | GeFile[] | Date

export interface Model {
   [fieldName: string]: ModelValue
}

export type CompositeModel = {
   [fieldName: string]: ModelValue | Model | CompositeModel
}

export type View<T = unknown> = {
   readonly data: T[]
   readonly pagination: ViewPagination
}

export const formatMiscItem = (item: MiscItem | MiscItem[] | null): number | number[] | null => {
   if (item === null) {
      return null;
   }

   return Array.isArray(item) ? item.map((miscItem: MiscItem) => miscItem.id) : item.id;
};

/**
 * Получает полную локальную дату в виде строки
 *
 * @param dateStr - строка даты в формате - "2021-06-29 12:09:54+05"
 * @return строка в формате 29.06.2021 12:10
 */
export const formatDateTimeString = (dateStr: string): string => {
   return formatHandler(() => format(roundToNearestMinutes(parseISO(dateStr)), 'dd.MM.yyyy HH:mm'));
};

/**
 * Получает сокращенную локальную дату в виде строки
 *
 * @param dateStr - строка даты в формате - "2021-06-29 12:09:54+05"
 * @return строка в формате 29.06.2021
 */
export const formatDateString = (dateStr: string): string => {
   return formatHandler(() => format(roundToNearestMinutes(parseISO(dateStr)), 'dd.MM.yyyy'));
};

export const formatDate = (date: Date): string => {
   return formatHandler(() => format(date, 'dd.MM.yyyy'));
};

/**
 * Получает полную локальную дату в виде строки
 *
 * @param date - объект даты
 * @return строка в формате 29.06.2021 12:10
 */
export const formatDateTime = (date: Date): string => {
   return formatHandler(() => format(date, 'dd.MM.yyyy HH:mm'));
};

export const formatTime = (date: Date): string => {
   return formatHandler(() => format(date, 'HH:mm'));
};

export const formatStringDateWithoutYear = (date: Date): string => {
   return formatHandler(() => `${format(date, 'd')} ${getInflectedMonthNameByNumber(date.getMonth())}`);
};

export const formatToJsonDate = (date: Date): string => {
   return formatHandler(() => format(date, 'yyyy-MM-dd'));
};

export const formatToJsonDateTime = (date: Date): string => {
   return formatHandler(() => format(date, 'yyyy-MM-dd HH:mm'));
};

/**
 * Преобразует строковую дату в объект
 *
 * @param dateStr - строка даты в формате - "2021-06-29"
 */
export const parseDate = (dateStr: string): Date => {
   return formatHandler(() => roundToNearestMinutes(parseISO(dateStr)));
};

export const parseNullableDateString = (dateStr: string | null): Date | null => {
   return dateStr === null ? null : parseDate(dateStr);
};

export const isDateString = (dateStr: string | null): boolean => {
   return dateStr === null ? false : isDate(parseDate(dateStr));
};

const formatHandler = <T> (format: () => T): T => {
   try {
      return format();
   } catch (e) {
      throw new LogicError('Неверный формат даты');
   }
};
