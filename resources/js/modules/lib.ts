import { LogicError } from '@/modules/errors/LogicError';
import cloneDeep from 'lodash/cloneDeep';

/**
 * Отключает стандартные действия переноса в браузере
 */
export const clearDefaultDropEvents = (): void => {
   const events = ['dragenter', 'dragover', 'dragleave', 'drop'];
   events.forEach(eventName => {
      document.addEventListener(eventName, event => {
         event.preventDefault();
         event.stopPropagation();
      });
   });
};

/**
 * Устанавливает значение в Map, если по данному ключу
 * не записано значение, иначе выводит алерт с ошибкой
 *
 * @param map - Map, в которой ищется элемент
 * @param key - ключ для поиска
 * @param value - устанавливаемое значение
 */
export function safeMapSetter<T, V>(map: Map<T, V>, key: T, value: V): void {
   if (map.has(key)) {
      // console.error(map);
      throw new LogicError(`Элемент Map по ключу: ${key} уже установлен`);
   } else {
      map.set(key, value);
   }
}

/**
 * Получает значение в переданной Map по заданному ключу или
 * вызывает алерт с ошибкой, если значение не найдено
 *
 * @param map - Map, в которой ищется элемент
 * @param key - ключ для поиска
 */
export function safeMapGetter<T, V>(map: Map<T, V>, key: T): V {
   const element: V = map.get(key) as V;
   if (element === undefined) {
      // console.error(map);
      throw new LogicError(`Не найдет элемент в Map по ключу: ${key}`);
   }
   return element;
}

/**
 * Получает регулярное выражение из строки
 *
 * @param regStr - строка с регулярным выражением
 */
export function getRegExpFromString(regStr: string): RegExp {
   const regBody: string = regStr.substr(1, regStr.lastIndexOf('$'));
   const params: string = regStr.substr(regStr.lastIndexOf('/') + 1);

   return params.length > 0 ? new RegExp(regBody, params) : new RegExp(regBody);
}

/**
 * Получает строку с измененной первой буквой на заглавную
 */
export const ucFirst = (str: string): string => {
   if (!str) return str;

   return str[0].toUpperCase() + str.slice(1);
};

export const deepClone = <T = unknown> (obj: T): T => {
   return cloneDeep(obj);
};

export const clone = <T = unknown> (obj: T): T => {
   return Object.assign({}, obj);
};

/**
 * Получает случайную строку длиной 5 символов
 */
export const getRandomString = (): string => {
   return Math.random().toString(36).substring(2, 7);
};

