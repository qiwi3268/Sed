import { ApiHandler } from '@/modules/api/ApiHandler';
import {
   transformConferenceResponse
} from '@/modules/forms/mapping/conferences';
import { ConferenceRead, ConferenceResponse } from '@/types/conferences';

export class ConferenceReadGetter extends ApiHandler<ConferenceResponse, ConferenceRead> {
   protected handleSuccessResponse(conference: ConferenceResponse): ConferenceRead {
      return transformConferenceResponse(conference);
   }
}
