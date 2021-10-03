import { FileFieldRules } from '@/types/api';
import { openError } from '@/modules/widgets/error';

/**
 * Проверяет загружаемые файлы на соответствие правилам валидации
 *
 * @param files - загружаемые файлы
 * @param allowableExtensions - доступные расширения
 * @param maxSize - максимальный размер файла
 * @param forbiddenSymbols - запрещенные символы в названии
 */
export const checkFiles = (files: File[], { allowableExtensions, maxSize, forbiddenSymbols }: FileFieldRules): boolean => {
   const errorTitle = 'Ошибка при загрузке файлов';

   if (!files || files.length === 0) {
      openError(errorTitle, 'Не выбраны файлы для загрузки');
      return false;
   } else if (!isValidExtensions(files, allowableExtensions)) {
      openError(errorTitle, 'Файл содержит недопустимое расширение');
      return false;
   } else if (!isNotEmpty(files)) {
      openError(errorTitle, 'Размер файлов должен быть больше 1 Кб');
      return false;
   } else if (!isValidSizes(files, maxSize)) {
      openError(errorTitle, `Максимальный размер файлов для загрузки: ${maxSize} МБ`);
      return false;
   } else if (!isNotContainsForbiddenSymbols(files, forbiddenSymbols)) {
      openError(errorTitle, 'Название файла содержит запрещенные символы');
      return false;
   }

   return true;
};

/**
 * Проверяет файлы на соответвия доступным расширения
 *
 * @param files - загружаемые файлы
 * @param allowableExtensions - доступные расширения
 */
const isValidExtensions = (files: File[], allowableExtensions: string[] | null): boolean => {
   if (!allowableExtensions || allowableExtensions.length === 0) {
      return true;
   }

   const sigExtensions = ['sig', 'p7z'];

   return files.every(file => {
      const nameParts: string[] = file.name.split('.');
      return nameParts
         .map(namePart => namePart.toLocaleLowerCase())
         .filter(namePart => !sigExtensions.includes(namePart))
         .filter(namePart => allowableExtensions.includes(namePart))
         .length === 1;
   });
};

const isNotEmpty = (files: File[]): boolean => {
   return files.every(file => file.size > 1024);
};

/**
 * Проверяет файлы на превышение максимального размера
 *
 * @param files - загружаемые файлы
 * @param maxFileSize - максимальный размер
 */
const isValidSizes = (files: File[], maxFileSize: number): boolean => {
   return files.every(file => file.size / 1024 / 1024 <= maxFileSize);
};

/**
 * Проверяет файлы на содержание в названии запрещенных символов
 *
 * @param files - загружаемые файлы
 * @param forbiddenSymbols - запрещенные символы
 */
const isNotContainsForbiddenSymbols = (files: File[], forbiddenSymbols: string[]): boolean => {
   if (!forbiddenSymbols || forbiddenSymbols.length === 0) {
      return true;
   }

   return files.every(file => {
      return file.name
         .split('')
         .every(character => !forbiddenSymbols.includes(character));
   });
};
