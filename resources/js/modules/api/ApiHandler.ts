import axios, { AxiosError, AxiosRequestConfig, AxiosResponse } from 'axios';
import { LogicError } from '@/modules/errors/LogicError';
import { ApiError } from '@/modules/errors/ApiError';
import { RouteLocationRaw } from 'vue-router';

/**
 * Описывает объект, полученный в результате запроса с ошибкой
 */
export type ErrorResponse = {

   /**
    * Сообщение с результатом запроса
    */
   message: string

   /**
    * Массив с ошибками
    */
   errors: string[][]

   /**
    * Код ошибки
    */
   code: string
};

/**
 * Описывает параметры гет запроса
 */
export interface JsonParams {
   [param: string]: string | number | boolean | null | string[] | number[] | boolean[] | File[] | JsonParams
}

export type UploadCallback = (event: ProgressEvent) => void;

/**
 * Коды ошибок, которые могут прийти с апи
 */
export enum ApiClientErrorCodes {
   NoSigners = 'nsec'
}

export type ApiHandlerOptions = {
   fallbackRoute?: RouteLocationRaw
}

/**
 * Представляет собой обработчик апи
 * T - Тип объекта полученного с сервера
 * F - Тип преобразованного объекта
 * E - Тип объекта ошибки
 */
export abstract class ApiHandler<T = unknown, F = T, E = ErrorResponse> {
   /**
    * Ошибка, полученная в результате запроса
    */
   protected error!: AxiosError<ErrorResponse>;

   /**
    * Данные об ошибке, полученные с сервера
    */
   protected errorResponse!: ErrorResponse;

   /**
    * Конфиг для запроса на апи
    */
   protected config: AxiosRequestConfig = {};

   protected isBinaryResponse = false;

   protected options: ApiHandlerOptions = {};

   protected isRepeated = false;

   /**
    * Конфигурирует гет запрос
    *
    * @param params - параметры запроса
    * @param url - путь для запроса
    * @param uploadCallback - колбэк для обработки запроса в процессе отправки данных
    */
   public buildGetConfig(
      url: string,
      params: JsonParams,
      uploadCallback: UploadCallback | null = null
   ): ApiHandler<T, F, E> {
      this.config = Object.assign(this.config, {
         method: 'get',
         url,
         params
      });

      if (uploadCallback) {
         this.config.onUploadProgress = progressEvent => uploadCallback(progressEvent);
      }

      return this;
   }

   /**
    * Конфигурирует пост запрос с форм датой
    *
    * @param url - путь для запроса
    * @param data - данные для отправки
    * @param uploadCallback - колбэк для обработки запроса в процессе отправки данных
    */
   public buildPostConfig(
      url: string,
      data: FormData,
      uploadCallback: UploadCallback | null = null
   ): ApiHandler<T, F, E> {
      this.config = Object.assign(this.config, {
         method: 'post',
         url,
         data,
         headers: {
            'Content-Type': 'multipart/form-data'
         }
      });

      if (uploadCallback) {
         this.config.onUploadProgress = progressEvent => uploadCallback(progressEvent);
      }

      return this;
   }

   constructor(options?: ApiHandlerOptions) {
      if (options) {
         this.options = options;
      }
   }

   /**
    * Конфигурирует пост запрос с json
    *
    * @param params - параметры запроса
    * @param url - путь для запроса
    * @param uploadCallback - колбэк для обработки запроса в процессе отправки данных
    */
   public buildPostJSONConfig(
      url: string,
      params: JsonParams,
      uploadCallback: UploadCallback | null = null
   ): ApiHandler<T, F, E> {
      this.config = Object.assign(this.config, {
         method: 'post',
         url,
         data: params,
         headers: {
            'Content-Type': 'application/json'
         }
      });

      if (uploadCallback) {
         this.config.onUploadProgress = progressEvent => uploadCallback(progressEvent);
      }

      return this;
   }

   /**
    * Конфигурирует гет запрос для получения бинарных данных
    *
    * @param url - путь для запроса
    * @param uploadCallback - колбэк для обработки запроса в процессе отправки данных
    */
   public buildGetBinaryDataConfig(
      url: string,
      uploadCallback: UploadCallback | null = null
   ): ApiHandler<T, F, E> {
      this.config = Object.assign(this.config, {
         method: 'get',
         url,
         responseType: 'arraybuffer'
      });

      if (uploadCallback) {
         this.config.onUploadProgress = progressEvent => uploadCallback(progressEvent);
      }

      this.isBinaryResponse = true;

      return this;
   }

   /**
    * Устанавливает время ожидания ответа для запроса
    */
   public setTimeout(milliseconds: number): ApiHandler<T, F, E> {
      this.config.timeout = milliseconds;
      return this;
   }

   private async handleResponse(response: AxiosResponse) {
      if (this.isValidSuccessResponse(response)) {
         const apiData = this.isBinaryResponse ? response.data : response.data.data;

         // Для теста
         // if (this.config.url === '/api/signatureSessions/navigation/items/waitingAction') {
         // }

         return Promise.resolve(this.handleSuccessResponse(apiData));
      } else if (response.status === 204) {
         return Promise.reject(response);
      } else {
         // console.error(response);
         // reject(response);
         throw new ApiError(
            'Ошибка при выполнении запроса',
            'Не ожидалось ответа с таким статусом',
            new Error()
         );
         // return Promise.reject(response);
      }
   }

   private async handleReject(error: AxiosError<ErrorResponse>) {
      this.error = error as AxiosError<ErrorResponse>;

      if (this.isValidErrorResponse(error)) {
         this.errorResponse = this.error.response!.data;

         if (this.error.response?.status.toString().startsWith('4')) {
            return Promise.reject(this.clientFatalError());
         } else {
            return Promise.reject(this.serverFatalError());
         }
      } else if (error.response) {
         if (error.response.status === 500) {
            return Promise.reject(this.serverFatalError());
         } else {
            throw new ApiError('Непредвиденная ошибка при выполнении запроса', 'Обратитесь к администратору', error);
         }
      } else if (error.request) {
         throw new ApiError('Не получен ответ от сервера', 'Обратитесь к администратору', error);
      } else {
         throw new ApiError('Непредвиденная ошибка при выполнении запроса', 'Обратитесь к администратору', error);
      }
   }

   /**
    * Отправляет и обрабатывает запрос на api
    *
    * @returns Промис с данными полученными с api
    */
   public async send(): Promise<F> {
      if (!this.config.method || !this.config.url) {
         throw new LogicError('Отсутствует конфиг запроса');
      }

      // return new Promise<F>((resolve, reject) => {
      // console.log('request');

      return axios.request(this.config)
         .then((response: AxiosResponse) => {
            return this.handleResponse(response);
         })
         .catch((error: AxiosError<ErrorResponse>) => {
            return this.handleReject(error);
         });
   }

   /**
    * Проверяет содержит ли успешный ответ с api ожидаемые данные
    *
    * @param response - ответ с api
    */
   private isValidSuccessResponse(response: AxiosResponse): boolean {
      return (
         response.status === 200 &&
         (this.isValidJSONResponse(response) || this.isBinaryResponse)
      );
   }

   private isValidJSONResponse(response: AxiosResponse): boolean {
      return (
         response.data.hasOwnProperty('message') &&
         response.data.hasOwnProperty('data')
      );
   }

   protected handleSuccessResponse(data: T): F {
      return data as unknown as F;
   }

   /**
    * Проверяет содержит ли объект ошибки с api ожидаемые данные
    *
    * @param error - ответ с api
    */
   private isValidErrorResponse(error: AxiosError): boolean {
      return (
         !!error.response &&
         !!error.response.data &&
         error.response.data.hasOwnProperty('message') &&
         error.response.data.hasOwnProperty('errors') &&
         error.response.data.hasOwnProperty('code')
      );
   }

   /**
    * Обработка клиентской ошибки запроса по умолчанию
    */
   protected clientFatalError(): E | ErrorResponse {
      const errorCode: string = this.error.response?.data.code ?? '';

      if (
         this.error.response?.status === 422 &&
         errorCode !== '' &&
         this.getHandledErrorCodes().includes(errorCode)
      ) {
         return this.clientInvalidArgumentError();
      } else {
         return this.defaultError();
      }
   }

   /**
    * Получает массив обрабатываемых ошибок для апи
    */
   protected getHandledErrorCodes(): string[] {
      return [];
   }

   /**
    * Обрабатывает случай, когда входные параметры со стороны Js невалидны.
    * Это может быть связано с ошибкой при входной валидации или ошибкой в логике
    */
   protected clientInvalidArgumentError(): E | ErrorResponse {
      return this.defaultError();
   }

   /**
    * Обработка серверной ошибки запроса по умолчанию
    */
   protected serverFatalError(): E | ErrorResponse {
      return this.defaultError();
   }

   /**
    * Вывод ошибки по умолчанию
    */
   private defaultError(): ErrorResponse {
      throw new ApiError(
         'Непредвиденная ошибка при выполнении запроса',
         'Обратитесь к администратору',
         this.error
      );
   }
}
