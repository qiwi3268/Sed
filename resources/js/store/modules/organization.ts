import { ActionContext } from 'vuex';
import { RootState } from '@/store';
import { MiscItem } from '@/store/modules/misc';
import { Api } from '@/modules/api/Api';
import { successOrEmptyArray } from '@/modules/api/wrappers';

export type OrganizationState = {
   users: Map<number, MiscItem>
   fetching: boolean
   usersFetched: boolean
}

type Context = ActionContext<OrganizationState, RootState>

export default {
   namespaced: true,
   state: (): OrganizationState => ({
      users: new Map(),
      fetching: false,
      usersFetched: false
   }),
   mutations: {},
   actions: {
      setUser({ state }: Context, user: MiscItem): void {
         if (!state.users.has(user.id)) {
            state.users.set(user.id, user);
         }
      },
      async fetchUsers({ state }: Context): Promise<void> {
         if (!state.usersFetched && !state.fetching) {
            state.fetching = true;

            const users = await successOrEmptyArray(Api.getUsers());
            state.users = new Map();
            users.forEach(user => state.users.set(user.id, user));

            state.usersFetched = true;
            state.fetching = false;
         }
      }
   },
   getters: {
      getUsers(state: OrganizationState): MiscItem[] {
         return [...state.users.values()];
      }
   }

};
