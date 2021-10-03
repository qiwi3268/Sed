import { ApiHandler } from '@/modules/api/ApiHandler';
import {
   transformPaginationResponse,
   transformSignSessionResponseCommonData
} from '@/modules/forms/mapping/sign-sessions';
import { View } from '@/modules/forms/formatting';
import { SignSessionsWaitingActionResponse, SignSessionWaitingAction } from '@/types/sign-sessions';

export class SignSessionsWaitingActionItemsGetter extends ApiHandler<SignSessionsWaitingActionResponse, View<SignSessionWaitingAction>> {
   protected handleSuccessResponse(data: SignSessionsWaitingActionResponse): View<SignSessionWaitingAction> {
      return {
         data: data.data.map(signSession => {
            return {
               ...transformSignSessionResponseCommonData(signSession),

               status: {
                  id: signSession.signature_session_status_id,
                  name: signSession.signature_session_status_name
               }
            };
         }),
         pagination: transformPaginationResponse(data)
      };
   }
}
