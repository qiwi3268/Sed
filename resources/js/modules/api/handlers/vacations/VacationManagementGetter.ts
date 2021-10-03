import { ApiHandler } from '@/modules/api/ApiHandler';
import { transformVacationFormResponseCommonData } from '@/modules/forms/mapping/vacations';
import { VacationFormEdit, VacationFormResponse } from '@/types/vacations';
import { getRandomString } from '@/modules/lib';

export class VacationManagementGetter extends ApiHandler<VacationFormResponse[], VacationFormEdit[]> {
   protected handleSuccessResponse(data: VacationFormResponse[]): VacationFormEdit[] {
      return data.map(vacation => {
         return {
            vacationId: vacation.id,
            ...transformVacationFormResponseCommonData(vacation),

            key: getRandomString(),
            changed: false
         };
      });
   }
}
