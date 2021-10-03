/**
 * Обрабатывает запрос на доступ
 *
 * @param guard
 * @return {Promise<boolean>}
 */
import { LogicError } from '@/modules/errors/LogicError';

export const actionGuard = async(guard: Promise<{ can: boolean }>): Promise<boolean> => {
   try {
      const response = await guard;
      return response.can;
   } catch {
      return false;
   }
};

/**
 * Обрабатывает успешное выполнение запроса
 * В случает ошибки возвращает пустой промис
 *
 * @param request - запрос
 * @param callback - действие, которое выполняется при успешном выполнении запроса
 */
export const handleSuccess = async(request: Promise<unknown>, callback: Function): Promise<void> => {
   try {
      await request;
      callback();
   } catch (e) {
   }
};

export const postProcess = async(request: () => Promise<unknown>, callback: Function): Promise<void> => {
   try {
      await request();
   } catch (e) {
   }

   callback();
};

export const safeExecute = async(request: Promise<unknown>): Promise<unknown> => {
   try {
      return await request;
   } catch (e) {
   }
};

export const successOrNull = async<T = unknown>(request: Promise<T>): Promise<T | null> => {
   try {
      return await request;
   } catch (e) {
      return null;
   }
};

export const successOrEmptyArray = async<T = unknown[]>(request: Promise<T>): Promise<T | []> => {
   try {
      return await request;
   } catch (e) {
      return [];
   }
};
