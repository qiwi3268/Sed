import { ActionContext } from 'vuex';
import store, { RootState } from '@/store';
import { mRequired, positive, unique, ValidationConfig } from '@/modules/forms/validators';
import { Api } from '@/modules/api/Api';
import { getRandomString } from '@/modules/lib';
import { getVacationNavigationRoute } from '@/router/vacations';
import { forceChangeRoute } from '@/router';
import { Toast } from '@/modules/notification/Toast';
import {
   VacationChanges,
   VacationForm,
   VacationFormEdit,
   VacationManagementCategories,
   VacationRead
} from '@/types/vacations';
import { actionGuard, successOrEmptyArray } from '@/modules/api/wrappers';

export type VacationState = {
   nextVacations: VacationFormEdit[]
   pastVacations: VacationFormEdit[]
   fetchedPastVacations: boolean
   vacationsRead: VacationRead[]
   usersOnVacation: string[]
   fetchingVacationsRead: boolean
   canManageVacations: boolean
   userOnVacationPercent: number | null
}

type Context = ActionContext<VacationState, RootState>

export default {
   namespaced: true,
   state: (): VacationState => ({
      nextVacations: [],
      pastVacations: [],
      fetchedPastVacations: false,
      vacationsRead: [],
      usersOnVacation: [],
      fetchingVacationsRead: false,
      canManageVacations: false,
      userOnVacationPercent: null
   }),
   mutations: {},
   actions: {
      getNextVacations({ state }: Context): Promise<VacationFormEdit[]> {
         state.nextVacations = [];
         return Api.getVacationManagement(VacationManagementCategories.Next)
            .then(response => state.nextVacations = response);
      },
      getPastVacations({ state }: Context): void {
         state.fetchedPastVacations = true;
         state.pastVacations = [];

         Api.getVacationManagement(VacationManagementCategories.Past)
            .then(response => state.pastVacations = response);
      },
      getForNext30DaysVacations({ dispatch }: Context): void {
         dispatch('handleVacationsReadFetching', Api.getForNext30DaysVacationsRead());
      },
      getVacationsForDate({ dispatch }: Context, { year, month }: { year: number, month: number }): void {
         dispatch('handleVacationsReadFetching', Api.getVacationsReadForYearAndMonth(year, month));
      },
      fetchUsersOnVacation({ state }: Context): void {
         state.usersOnVacation = [];

         Api.getUsersOnVacation()
            .then(response => state.usersOnVacation = response);
      },
      async handleVacationsReadFetching({ state }: Context, request: Promise<VacationRead[]>): Promise<void> {
         state.vacationsRead = [];
         state.fetchingVacationsRead = true;
         state.vacationsRead = await successOrEmptyArray(request);
         state.fetchingVacationsRead = false;
      },
      async checkCanManageVacations({ state }: Context): Promise<boolean> {
         state.canManageVacations = await actionGuard(Api.canUserManageVacations());
         return state.canManageVacations;
      },
      async fetchUserOnVacationPercent({ state }: Context): Promise<void> {
         state.userOnVacationPercent = (await Api.getUserOnVacationPercent()).percent;
      },
      async handleVacations({ state }: Context, { vacationsToSave, vacationsToDelete }: VacationChanges): Promise<void> {
         const changedVacations = state.nextVacations
            .filter(vacation => vacation.changed)
            .concat(state.pastVacations.filter(vacation => vacation.changed));

         try {
            const handledVacations = await Promise.all(
               vacationsToSave.map(vacation => Api.saveVacation(vacation))
                  .concat(changedVacations.map(vacation => Api.updateVacation(vacation)))
                  .concat(vacationsToDelete.map(vacation => Api.deleteVacation(vacation.vacationId)))
            );

            if (Array.isArray(handledVacations) && handledVacations.length > 0) {
               Toast.vacationsSaved();
            }
         } catch {
            Toast.failedVacationSave();
         }
         forceChangeRoute(getVacationNavigationRoute());
      },
      clearPastVacations({ state }: Context): void {
         state.pastVacations = [];
      }
   },
   getters: {
      getVacationEmptyForm(): VacationForm {
         return {
            employee: null,
            startAt: null,
            duration: 14,
            replacementEmployees: [],
            key: getRandomString()
         };
      },
      getVacationFormRules(): ValidationConfig {
         return {
            employee: { mRequired },
            startAt: { mRequired },
            duration: { mRequired, positive },
            replacementEmployees: { unique },
            key: { mRequired }
         };
      }

   }
};
