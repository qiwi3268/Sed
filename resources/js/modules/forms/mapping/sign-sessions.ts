import { JsonParams } from '@/modules/api/ApiHandler';
import { Fio } from '@/modules/utils/Fio';
import { getFileSizeString } from '@/modules/modals/files/utils';
import { formatDateTimeString, formatDateString, formatMiscItem } from '@/modules/forms/formatting';
import { SignState } from '@/store/modules/modals/files/signer';
import { transformValidation } from '@/modules/forms/mapping/signatures';
import {
   FinishedSigner, FinishedSignerResponse, SignerStates, SignSession,
   SignSessionCommonData, SignSessionCommonDataResponse,
   SignSessionDetails, SignSessionResponse,
   SignSessionSigners, SignSessionSignersResponse
} from '@/types/sign-sessions';
import { PaginationResponse, ViewPagination } from '@/types/api';

/**
 * Преобразует сессию подписания для апи создания
 */
export const transformSignSession = (form: SignSession): JsonParams => {
   return {
      title: form.title,
      signerIds: formatMiscItem(form.signerIds),
      originalStarPath: form.m1m1m1[0].starPath
   };
};

/**
 * Преобразует сессию подписания для апи сохранения подписи
 */
export const transformSignSessionSigning = (form: SignSessionDetails): JsonParams => {
   return {
      signatureSessionUuid: form.uuid,
      externalSignatureStarPath: form.file.signStarPath!
   };
};

/**
 * Преобразует пагинацию с сервера
 */
export const transformPaginationResponse = (data: PaginationResponse): ViewPagination => {
   return {
      perPage: data.per_page,
      currentPage: data.current_page,
      lastPage: data.last_page,
      links: data.links,
      total: data.total
   };
};

/**
 * Преобразует основные данные сессии подписания с сервера
 */
export const transformSignSessionResponseCommonData = (data: SignSessionCommonDataResponse): SignSessionCommonData => {
   return {
      uuid: data.uuid,
      title: data.title,
      createdAt: formatDateString(data.created_at),
      author: new Fio(data.author_last_name, data.author_first_name, data.author_middle_name).getShortFio()
   };
};

/**
 * Преобразует полные данные сессии подписания с сервера
 */
export const transformSignSessionDetails = (data: SignSessionResponse): SignSessionDetails => {
   return {
      ...transformSignSessionResponseCommonData(data),

      file: {
         starPath: data.file_star_path,
         originalName: data.file_original_name,
         sizeString: getFileSizeString(data.file_size),
         validationResult: [],
         signState: SignState.NotSigned
      },

      statusId: data.status_id,
      statusName: data.status_name

   };
};

/**
 * Преобразует коллекцию подписантов сессии подписания с сервера
 */
export const transformSignSessionSigners = (signers: SignSessionSignersResponse): SignSessionSigners => {
   return {
      signed: signers.signed.map(signer => transformFinishedSigner(signer)),
      unsigned: signers.unsigned.map(signer => {
         return {
            fio: new Fio(signer.last_name, signer.first_name, signer.middle_name).getShortFio(),
            statusId: signer.status_id,
            statusName: signer.status_name
         };
      })
   };
};

const transformFinishedSigner = (signer: FinishedSignerResponse): FinishedSigner => {
   const fio = new Fio(signer.last_name, signer.first_name, signer.middle_name);
   const validationResult = transformValidation(signer.validation_result);

   let signerState;
   if (validationResult.signers.length === 1 && validationResult.signers[0].fio === fio.getLongFio()) {
      signerState = SignerStates.Valid;
   } else {
      signerState = SignerStates.Invalid;
   }

   return {
      fio: fio.getShortFio(),
      signedAt: formatDateTimeString(signer.signed_at),
      validationResult,
      signerState
   };
};
