import { ApiHandler } from '@/modules/api/ApiHandler';
import { parseDate } from '@/modules/forms/formatting';
import { Fio } from '@/modules/utils/Fio';
import { VacationRead, VacationReadResponse } from '@/types/vacations';

export class VacationsReadGetter extends ApiHandler<VacationReadResponse[], VacationRead[]> {
   protected handleSuccessResponse(data: VacationReadResponse[]): VacationRead[] {
      return data.map(vacation => {
         return {
            employee: {
               id: vacation.user.id,
               label:
                  new Fio(vacation.user.last_name, vacation.user.first_name, vacation.user.middle_name)
                     .getShortFio()
            },
            startAt: parseDate(vacation.start_at),
            duration: vacation.duration,
            replacementEmployees: vacation.replacements.map(user => ({
               id: user.id,
               label: new Fio(user.last_name, user.first_name, user.middle_name).getShortFio()
            })),

            finishedAt: parseDate(vacation.finish_at),
            goingToWorkAt: parseDate(vacation.going_to_work_at)
         };
      });
   }
}
