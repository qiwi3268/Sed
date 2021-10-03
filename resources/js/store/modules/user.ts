import { ActionContext } from 'vuex';
import store, { RootState } from '@/store';
import { UserResponse } from '@/types/api';
import { Fio } from '@/modules/utils/Fio';
import { Api } from '@/modules/api/Api';
import { Toast } from '@/modules/notification/Toast';

type User = {
   uuid: string
   fio: string
}

export type UserState = {
   user: User | null
}

type Context = ActionContext<UserState, RootState>

export default {
   namespaced: true,
   state: (): UserState => ({
      user: null
   }),
   mutations: {
      setUser(state: UserState, user: UserResponse): void {
         if (user === null) {
            state.user = null;
         } else {
            state.user = {
               uuid: user.uuid,
               fio: new Fio(user.last_name, user.first_name, user.middle_name).getLongFio()
            };
         }
      }
   },
   actions: {
      async fetchUser({ commit }: Context): Promise<void> {
         try {
            commit('setUser', await Api.fetchUser());
         } catch {
            Toast.getUserError();
            await store.dispatch('auth/logout');
         }
      },
      async getUser({ state, dispatch }: Context): Promise<User | null> {
         if (!state.user) {
            await dispatch('fetchUser');
         }
         return state.user;
      },
      async getUserUuid({ dispatch }: Context): Promise<string> {
         const user = await dispatch('getUser');
         return user.uuid;
      }
   },
   getters: {
      getUserFio(state: UserState): string {
         return state.user?.fio ?? '';
      }
   }
};
