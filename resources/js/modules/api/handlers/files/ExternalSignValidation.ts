import { ApiClientErrorCodes, ApiHandler, ErrorResponse } from '@/modules/api/ApiHandler';
import { SignState } from '@/store/modules/modals/files/signer';
import { transformSignValidation } from '@/modules/forms/mapping/signatures';
import { Toast } from '@/modules/notification/Toast';

/**
 * Информация о сертификате
 */
export type CertificateInfo = {
   /**
    * Серийный номер
    */
   readonly serial: string
   /**
    * Издатель
    */
   readonly issuer: string
   /**
    * Владелец
    */
   readonly subject: string

   /**
    * Диапазон дат, в котором сертификат действителен
    */
   readonly validRange: string
}

export type ValidationResultResponse = {

   readonly subject: {
      readonly firstName: string
      readonly lastName: string
      readonly middleName: string
   }

   /**
    * Информация о сертификате
    */
   readonly certificate: CertificateInfo

   readonly certificateMessage: string
   readonly certificateResult: boolean

   readonly signatureMessage: string
   readonly signatureResult: boolean
}

/**
 * Описывает результат валидации подписей файла
 */
export type ValidationResult = {
   /**
    * ФИО подписанта
    */
   readonly fio: string

   /**
    * Информация о сертификате
    */
   readonly certificate: CertificateInfo

   readonly certificateMessage: string
   readonly certificateResult: boolean

   readonly signatureMessage: string
   readonly signatureResult: boolean
}

export type ValidationResponse = {
   readonly result: SignState
   readonly signers: ValidationResultResponse[]
}

export type Validation = {
   readonly result: SignState
   readonly signers: ValidationResult[]
}

export type SignValidationResponse = {
   readonly starPath: string
   readonly validationResult: ValidationResponse
}

export type SignValidation = {
   readonly starPath: string
   readonly validationResult: Validation
}

export class ExternalSignValidation extends ApiHandler<SignValidationResponse, SignValidation> {
   protected handleSuccessResponse(data: SignValidationResponse): SignValidation {
      return transformSignValidation(data);
   }

   /**
    * Обрабатывает клиентскую ошибку при валидации открепленной подписи
    */
   protected clientInvalidArgumentError(): ErrorResponse {
      if (this.errorResponse.code === ApiClientErrorCodes.NoSigners) {
         // console.error(this.errorResponse);
         Toast.externalSignValidationError(this.errorResponse.message);
      }

      return this.errorResponse;
   }

   protected getHandledErrorCodes(): string[] {
      return [
         ApiClientErrorCodes.NoSigners
      ];
   }
}

