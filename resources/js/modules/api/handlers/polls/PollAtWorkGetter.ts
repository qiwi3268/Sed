import { ApiHandler } from '@/modules/api/ApiHandler';
import { parseDate, parseNullableDateString } from '@/modules/forms/formatting';
import { transformPollAtWorkOptions } from '@/modules/forms/mapping/polls';
import { PollAtWork, PollAtWorkResponse } from '@/types/polls';

export class PollAtWorkGetter extends ApiHandler<PollAtWorkResponse, PollAtWork | null> {
   protected handleSuccessResponse(data: PollAtWorkResponse): PollAtWork | null {
      if (Array.isArray(data) && data.length === 0) {
         return null;
      } else {
         return {
            createdAt: parseDate(data.created_at),
            finishedAt: parseNullableDateString(data.finished_at),
            statusId: data.status_id,
            statusName: data.status_name,
            options: transformPollAtWorkOptions(data)
         };
      }
   }
}

