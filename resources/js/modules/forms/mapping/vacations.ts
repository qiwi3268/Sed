import { JsonParams } from '@/modules/api/ApiHandler';
import isEqual from 'lodash/isEqual';
import { miscComparator } from '@/store/modules/misc';
import { Fio } from '@/modules/utils/Fio';
import { formatToJsonDate, parseDate } from '@/modules/forms/formatting';
import { VacationCommonData, VacationForm, VacationFormEdit, VacationFormResponse } from '@/types/vacations';

/**
 * Преобразует форму отпуска для апи создания
 */
export const transformVacation = (form: VacationForm): JsonParams => {
   return {
      userId: form.employee!.id,
      startAt: formatToJsonDate(form.startAt!),
      duration: form.duration,
      replacementIds: form.replacementEmployees.map(user => user.id)
   };
};

export const transformVacationManagement = (form: VacationFormEdit): JsonParams => {
   return {
      ...transformVacation(form),
      vacationId: form.vacationId
   };
};

export const transformVacationFormResponseCommonData = (vacation: VacationFormResponse): VacationCommonData => {
   return {
      employee: {
         id: vacation.user.id,
         label:
            new Fio(vacation.user.last_name, vacation.user.first_name, vacation.user.middle_name)
               .getLongFio()
      },
      startAt: parseDate(vacation.start_at),
      duration: vacation.duration,
      replacementEmployees: vacation.replacements.map(user => ({
         id: user.id,
         label: new Fio(user.last_name, user.first_name, user.middle_name).getLongFio()
      }))
   };
};

export const isVacationsEqual = (vac1: VacationForm, vac2: VacationForm): boolean => {
   let result = false;

   if (
      vac1.employee!.id === vac2.employee!.id &&
      formatToJsonDate(vac1.startAt!) === formatToJsonDate(vac2.startAt!) &&
      vac1.duration === vac2.duration &&
      isEqual(vac1.replacementEmployees.slice().sort(miscComparator), vac2.replacementEmployees.slice().sort(miscComparator)) &&
      vac1.key === vac2.key
   ) {
      result = true;
   }

   return result;
};
