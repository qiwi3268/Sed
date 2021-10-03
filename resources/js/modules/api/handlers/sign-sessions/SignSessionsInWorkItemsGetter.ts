import { ApiHandler } from '@/modules/api/ApiHandler';
import { Fio } from '@/modules/utils/Fio';
import {
   transformPaginationResponse,
   transformSignSessionResponseCommonData
} from '@/modules/forms/mapping/sign-sessions';
import { View } from '@/modules/forms/formatting';
import { SignSessionInWork, SignSessionsInWorkResponse } from '@/types/sign-sessions';

export class SignSessionsInWorkItemsGetter extends ApiHandler<SignSessionsInWorkResponse, View<SignSessionInWork>> {
   protected handleSuccessResponse(data: SignSessionsInWorkResponse): View<SignSessionInWork> {
      return {
         data: data.data.map(signSession => {
            return {
               ...transformSignSessionResponseCommonData(signSession),

               status: {
                  id: signSession.signature_session_status_id,
                  name: signSession.signature_session_status_name
               },
               signers: signSession.signers.map(signer => ({
                  fio: new Fio(signer.last_name, signer.first_name, signer.middle_name).getShortFio(),
                  signStatusId: signer.signature_session_signer_status_id
               }))

            };
         }),
         pagination: transformPaginationResponse(data)

      };
   }
}
