import { ApiHandler } from '@/modules/api/ApiHandler';
import { Fio } from '@/modules/utils/Fio';
import {
   transformPaginationResponse,
   transformSignSessionResponseCommonData
} from '@/modules/forms/mapping/sign-sessions';
import { formatDateString, View } from '@/modules/forms/formatting';
import { SignSessionFinished, SignSessionFinishedResponse } from '@/types/sign-sessions';

export class SignSessionsFinishedItemsGetter extends ApiHandler<SignSessionFinishedResponse, View<SignSessionFinished>> {
   protected handleSuccessResponse(data: SignSessionFinishedResponse): View<SignSessionFinished> {
      return {
         data: data.data.map(signSession => {
            return {
               ...transformSignSessionResponseCommonData(signSession),

               signedAt: formatDateString(signSession.finished_at),

               signers: signSession.signers.map(signer => ({
                  fio: new Fio(signer.last_name, signer.first_name, signer.middle_name).getShortFio()
               }))
            };
         }),
         pagination: transformPaginationResponse(data)

      };
   }
}
