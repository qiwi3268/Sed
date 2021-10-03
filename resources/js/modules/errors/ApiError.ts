import { Toast } from '@/modules/notification/Toast';

/**
 * Представляет собой непредвиденную ошибку с апи
 */
export class ApiError extends Error {
   public constructor(summary: string, detail: string, error: Error) {
      super(summary);
      Toast.apiError(summary, detail);
      // console.error(error);
   }
}
