import { ActionContext } from 'vuex';
import { RootState } from '@/store';
import { Api } from '@/modules/api/Api';
import { safeMapGetter } from '@/modules/lib';
import { successOrEmptyArray } from '@/modules/api/wrappers';

export type MiscItem = {
   readonly label: string
   readonly id: number
}

type MiscAlias = string

type SetMiscItemPayload = {
   miscAlias: string
   item: MiscItem
}

export type MiscState = {
   readonly fetchedMiscs: Set<MiscAlias>
   readonly fetching: Set<MiscAlias>
   readonly singleMiscs: Map<MiscAlias, Map<number, MiscItem>>
}

type Context = ActionContext<MiscState, RootState>

export default {
   namespaced: true,
   state: (): MiscState => ({
      fetchedMiscs: new Set(),
      fetching: new Set(),
      singleMiscs: new Map()
   }),
   mutations: {
   },
   actions: {
      setMiscItem({ state }: Context, { miscAlias, item }: SetMiscItemPayload): void {
         if (state.singleMiscs.has(miscAlias)) {
            state.singleMiscs.get(miscAlias);
         } else {
            state.singleMiscs.set(miscAlias, new Map());
         }

         const misc = safeMapGetter(state.singleMiscs, miscAlias);
         misc.set(item.id, item);
      },
      async fetchSingleMisc({ state }: Context, miscAlias: string): Promise<void> {
         if (!state.fetchedMiscs.has(miscAlias) && !state.fetching.has(miscAlias)) {
            state.fetching.add(miscAlias);

            const items = await successOrEmptyArray(Api.getSingleMisc(miscAlias));
            const misc = new Map();
            items.forEach(item => misc.set(item.id, item));
            state.singleMiscs.set(miscAlias, misc);

            state.fetching.delete(miscAlias);
            state.fetchedMiscs.add(miscAlias);
         }
      }
   },
   getters: {
      getSingleMiscItems(state: MiscState): (miscAlias: string) => MiscItem[] {
         return (miscAlias) => state.singleMiscs.has(miscAlias)
            ? [...safeMapGetter(state.singleMiscs, miscAlias).values()]
            : [];
      },
      isMiscFetched(state: MiscState): (miscAlias: string) => void {
         return (miscAlias) => state.fetchedMiscs.has(miscAlias);
      }
   }
};

export const miscComparator = (item1: MiscItem, item2: MiscItem): number => {
   if (item1.id === item2.id) {
      return 0;
   }

   return item1.id > item2.id ? 1 : -1;
};
