import omit from 'lodash/omit';
import { Fio } from '@/modules/utils/Fio';
import {
   SignValidation,
   SignValidationResponse, Validation, ValidationResponse
} from '@/modules/api/handlers/files/ExternalSignValidation';

export const transformSignValidation = (data: SignValidationResponse): SignValidation => {
   return {
      starPath: data.starPath,
      validationResult: transformValidation(data.validationResult)
   };
};

export const transformValidation = (data: ValidationResponse): Validation => {
   return {
      result: data.result,
      signers: data.signers.map(signer => {
         return {
            ...omit(signer, ['subjectFirstName', 'subjectLastName', 'subjectMiddleName']),
            fio: new Fio(signer.subject.lastName, signer.subject.firstName, signer.subject.middleName).getLongFio()
         };
      })
   };
};
