import { ApiHandler } from '@/modules/api/ApiHandler';
import { FioResponse } from '@/types/api';
import { Fio } from '@/modules/utils/Fio';

export class UsersOnVacationGetter extends ApiHandler<FioResponse[], string[]> {
   protected handleSuccessResponse(data: FioResponse[]): string[] {
      return data.map(user => new Fio(user.last_name, user.first_name, user.middle_name).getShortFio());
   }
}
