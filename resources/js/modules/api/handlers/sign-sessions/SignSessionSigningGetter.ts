import { ApiHandler } from '@/modules/api/ApiHandler';
import { transformSignSessionDetails } from '@/modules/forms/mapping/sign-sessions';
import { SignSessionDetails, SignSessionResponse } from '@/types/sign-sessions';

export class SignSessionSigningGetter extends ApiHandler<SignSessionResponse, SignSessionDetails> {
   protected handleSuccessResponse(data: SignSessionResponse): SignSessionDetails {
      return {
         ...transformSignSessionDetails(data)
      };
   }
}
