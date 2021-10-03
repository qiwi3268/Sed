import { SignState } from '@/store/modules/modals/files/signer';

/**
 * Возвращает размер файла в виде строки
 *
 * @param bytes - размер файла в байтах
 */
export const getFileSizeString = (bytes: number): string => {
   if (bytes === 0) return '0 Б';

   const labels = ['Б', 'Кб', 'Мб', 'Гб', 'Тб'];

   for (let i = labels.length - 1; i >= 0; i--) {
      const label = labels[i];
      const maxSizeForLabel = Math.pow(1024, i);
      if (bytes >= maxSizeForLabel) {
         const sizeStr = (bytes / maxSizeForLabel)
            .toFixed(2)
            .replace('.', ',');

         return `${sizeStr} ${label}`;
      }
   }

   return 'Некорректный размер';
};

/**
 * Возвращает тип иконки файла в зависимости от расширения в наименовании
 *
 * @param fileName - наименование файла
 */
export const getFileIcon = (fileName: string): string => {
   let result = 'file-alt';

   if (fileName.includes('.pdf')) {
      result = 'file-pdf';
   } else if (fileName.includes('.docx')) {
      result = 'file-word';
   } else if (fileName.includes('.xlsx')) {
      result = 'file-excel';
   }

   return result;
};

export const getSignStateIcon = (signState: SignState): string => {
   let result: string;

   switch (signState) {
   case SignState.Checking:
      result = 'spinner';
      break;
   case SignState.Valid:
      result = 'pen-alt';
      break;
   case SignState.Warning:
      result = 'exclamation';
      break;
   default:
      result = 'times';
      break;
   }

   return result;
};

export const getSignStateLabel = (signState: SignState): string => {
   let result: string;

   switch (signState) {
   case SignState.Checking:
      result = 'Проверка';
      break;
   case SignState.Valid:
      result = 'Подписано';
      break;
   case SignState.Warning:
      result = 'Ошибка сертификата';
      break;
   case SignState.Invalid:
      result = 'Подпись недействительна';
      break;
   default:
      result = 'Не подписано';
      break;
   }

   return result;
};
