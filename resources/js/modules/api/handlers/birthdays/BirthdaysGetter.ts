import { ApiHandler } from '@/modules/api/ApiHandler';
import { Fio } from '@/modules/utils/Fio';
import { parseDate } from '@/modules/forms/formatting';
import { FioResponse } from '@/types/api';

type BirthdaysResponse = FioResponse & {
   date_of_birth: string
}

export type UserBirthday = {
   fio: string
   dateOfBirth: Date
}

export class BirthdaysGetter extends ApiHandler<BirthdaysResponse[], UserBirthday[]> {
   protected handleSuccessResponse(data: BirthdaysResponse[]): UserBirthday[] {
      return data.map(user => ({
         fio: new Fio(user.last_name, user.first_name, user.middle_name).getLongFio(),
         dateOfBirth: parseDate(user.date_of_birth)
      }));
   }
}
