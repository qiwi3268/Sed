import { ApiClientErrorCodes, ApiHandler, ErrorResponse } from '@/modules/api/ApiHandler';
import {
   SignValidation,
   SignValidationResponse
} from '@/modules/api/handlers/files/ExternalSignValidation';
import { transformSignValidation } from '@/modules/forms/mapping/signatures';

export class InternalSignValidation extends ApiHandler<SignValidationResponse, SignValidation> {
   protected handleSuccessResponse(data: SignValidationResponse): SignValidation {
      return transformSignValidation(data);
   }

   /**
    * Обрабатывает клиентскую ошибку при валидации встроенной подписи
    */
   protected clientInvalidArgumentError(): ErrorResponse {
      // Если эта ошибка, то файл не содержит подпись
      if (this.errorResponse.code === ApiClientErrorCodes.NoSigners) {
         return this.errorResponse;
      } else {
         return super.clientInvalidArgumentError();
      }
   }

   protected getHandledErrorCodes(): string[] {
      return [
         ApiClientErrorCodes.NoSigners
      ];
   }
}
