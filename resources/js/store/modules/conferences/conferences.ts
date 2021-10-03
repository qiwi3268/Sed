import { ActionContext } from 'vuex';
import store, { RootState } from '@/store';
import {
   absoluteUrl,
   date,
   future,
   inputMaxLength,
   mRequired,
   textMaxLength,
   unique,
   ValidationConfig
} from '@/modules/forms/validators';
import { Api } from '@/modules/api/Api';
import router, { forceChangeRoute } from '@/router';
import { getConferenceNavigationRoute, returnToConferenceNavigation } from '@/router/conferences';
import { Toast } from '@/modules/notification/Toast';
import {
   ConferenceForm,
   ConferenceFormEdit,
   ConferenceRead,
   ConferenceViews
} from '@/types/conferences';
import { getCurrentYear } from '@/modules/utils/calendar';
import { actionGuard, handleSuccess } from '@/modules/api/wrappers';

export type ConferencesState = {
   canCreateConference: boolean
   canUpdateConference: boolean
   canDeleteConference: boolean
   conferenceCalendar: Map<string, string[]>
   selectedYear: number
   conferencesForDate: ConferenceRead[] | null

   selectedDate: Date

   conferenceRead: ConferenceRead | null
   conferenceEdit: ConferenceFormEdit | null
}

type Context = ActionContext<ConferencesState, RootState>

export default {
   namespaced: true,
   state: (): ConferencesState => ({
      canCreateConference: false,

      canUpdateConference: false,
      canDeleteConference: false,

      conferenceCalendar: new Map(),
      selectedYear: getCurrentYear(),
      conferencesForDate: null,
      selectedDate: new Date(),

      conferenceRead: null,
      conferenceEdit: null
   }),
   mutations: {},
   actions: {
      async checkCanCreateConferences({ state }: Context): Promise<boolean> {
         state.canCreateConference = await actionGuard(Api.canUserCreateConferences());
         return state.canCreateConference;
      },
      async saveConference(context: Context, model: ConferenceForm): Promise<void> {
         await handleSuccess(Api.saveConference(model), Toast.conferenceCreated);
         forceChangeRoute(getConferenceNavigationRoute());
      },
      async updateConference({ state, dispatch }: Context, model: ConferenceFormEdit): Promise<void> {
         const canUpdate = await dispatch('checkCanUserUpdateConference', model.uuid);
         if (canUpdate) {
            await handleSuccess(Api.updateConference(model), Toast.conferenceEdited);
         } else {
            Toast.noAccessToUpdateConference();
         }
         state.conferenceEdit = null;
         forceChangeRoute(getConferenceNavigationRoute());
      },
      async fetchConferenceRead({ state }: Context, conferenceUuid: string): Promise<void> {
         state.conferenceRead = null;
         state.conferenceRead = await Api.getConferenceRead(conferenceUuid);
      },
      async deleteConference({ state, dispatch }: Context, conferenceUuid: string): Promise<void> {
         const canDelete = await dispatch('checkCanUserDeleteConference', conferenceUuid);
         if (canDelete) {
            await handleSuccess(Api.deleteConference(conferenceUuid), Toast.conferenceDeleted);
         } else {
            Toast.noAccessToDeleteConference();
         }
         state.conferenceRead = null;
         returnToConferenceNavigation();
      },
      async fetchConferenceEdit({ state }: Context, conferenceUuid: string): Promise<void> {
         state.conferenceEdit = null;
         state.conferenceEdit = await Api.getConferenceEdit(conferenceUuid);
      },
      async checkCanUserUpdateConference({ state }: Context, conferenceUuid: string): Promise<boolean> {
         state.canUpdateConference = false;
         state.canUpdateConference = await actionGuard(Api.canUserUpdateConference(conferenceUuid));
         return state.canUpdateConference;
      },
      async checkCanUserDeleteConference({ state }: Context, conferenceUuid: string): Promise<boolean> {
         state.canDeleteConference = false;
         state.canDeleteConference = await actionGuard(Api.canUserDeleteConference(conferenceUuid));
         return state.canDeleteConference;
      },
      returnToSelectedView(): void {
         router.push(getConferenceNavigationRoute());
      },
      setSelectedDate({ state }: Context, date: Date): void {
         state.selectedDate = date;
      },
      updateConferencesForDateView({ state, dispatch }: Context): void {
         store.dispatch('navigation/changeSelectedView', ConferenceViews.ForDate);
         dispatch('clearCalendar');
         dispatch('fetchConferencesForDate', state.selectedDate);
      },
      fetchConferencesForDate({ state, commit, dispatch }: Context, date: Date): void {
         state.conferencesForDate = null;
         dispatch('setSelectedDate', date);

         Api.getConferenceForDateItems(state.selectedDate)
            .then(response => state.conferencesForDate = response);
      },
      clearCalendar({ state, dispatch }: Context): void {
         state.conferenceCalendar = new Map();
         dispatch('changeSelectedYear', state.selectedDate.getFullYear());
      },
      changeSelectedYear({ state, dispatch }: Context, year: number): void {
         state.selectedYear = year;
         dispatch('fetchConferenceCalendar');
      },
      fetchConferenceCalendar({ state }: Context): void {
         const selectedYear = state.selectedYear.toString();
         if (!state.conferenceCalendar.has(selectedYear)) {
            Api.getDatesWithConferencesByYear(selectedYear)
               .then(days => state.conferenceCalendar.set(selectedYear, days));
         }
      }
   },
   getters: {
      getEmptyConfidenceForm(): ConferenceForm {
         return {
            topic: null,
            startAt: null,
            conferenceForm: null,
            vksHref: null,
            vksConnectionResponsible: null,
            conferenceLocation: null,
            members: [],
            outerMembers: null,
            comment: null
         };
      },
      getConferenceRules(): ValidationConfig {
         return {
            topic: { mRequired, inputMaxLength },
            startAt: { mRequired, date, future },
            conferenceForm: { mRequired },
            vksHref: { textMaxLength, absoluteUrl },
            vksConnectionResponsible: {},
            conferenceLocation: { mRequired },
            members: { mRequired, unique },
            outerMembers: { textMaxLength },
            comment: { textMaxLength }
         };
      },
      getConferenceCalendar(state: ConferencesState): string[] {
         return state.conferenceCalendar.get(state.selectedYear.toString()) ?? [];
      }
   }
};

