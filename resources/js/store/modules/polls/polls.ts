import { ActionContext } from 'vuex';
import { RootState } from '@/store';
import { Api } from '@/modules/api/Api';
import { PollAtWork } from '@/types/polls';
import { successOrNull } from '@/modules/api/wrappers';

export type PollState = {
   pollAtWork: PollAtWork | null
   fetchingPool: boolean
}

type Context = ActionContext<PollState, RootState>

export default {
   namespaced: true,
   state: (): PollState => ({
      pollAtWork: null,
      fetchingPool: false
   }),
   mutations: {
   },
   actions: {
      async getPollAtWork({ state }: Context, date: Date): Promise<void> {
         state.pollAtWork = null;
         state.fetchingPool = true;
         state.pollAtWork = await successOrNull(Api.getPollAtWork(date));
         state.fetchingPool = false;
      }
   },
   getters: {

   }
};
