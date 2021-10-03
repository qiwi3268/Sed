import { ApiHandler } from '@/modules/api/ApiHandler';
import {
   transformConferenceEditResponse
} from '@/modules/forms/mapping/conferences';
import { ConferenceEditResponse, ConferenceFormEdit } from '@/types/conferences';

export class ConferenceEditGetter extends ApiHandler<ConferenceEditResponse, ConferenceFormEdit> {
   protected handleSuccessResponse(conference: ConferenceEditResponse): ConferenceFormEdit {
      return transformConferenceEditResponse(conference);
   }
}
