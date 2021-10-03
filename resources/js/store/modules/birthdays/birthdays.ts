import { ActionContext } from 'vuex';
import { RootState } from '@/store';
import { Api } from '@/modules/api/Api';
import { UserBirthday } from '@/modules/api/handlers/birthdays/BirthdaysGetter';

export type BirthdaysState = {
   birthdays: UserBirthday[]
}

type Context = ActionContext<BirthdaysState, RootState>

export default {
   namespaced: true,
   state: (): BirthdaysState => ({
      birthdays: []
   }),
   mutations: {},
   actions: {
      fetchBirthdays({ state }: Context): void {
         Api.getBirthdays()
            .then(response => state.birthdays = response);
      }
   },
   getters: {}
};
