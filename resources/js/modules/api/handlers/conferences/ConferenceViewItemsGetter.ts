import { ApiHandler } from '@/modules/api/ApiHandler';
import {
   transformConferenceResponse
} from '@/modules/forms/mapping/conferences';
import { ConferenceRead, ConferenceResponse } from '@/types/conferences';

export class ConferenceViewItemsGetter extends ApiHandler<ConferenceResponse[], ConferenceRead[]> {
   protected handleSuccessResponse(data: ConferenceResponse[]): ConferenceRead[] {
      return data.map(conference => transformConferenceResponse(conference));
   }
}
