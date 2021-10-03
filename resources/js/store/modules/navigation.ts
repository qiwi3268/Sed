import { ActionContext } from 'vuex';
import store, { RootState } from '@/store';
import { RouteLocationRaw } from 'vue-router';
import router from '@/router';
import { SignSessionViews } from '@/types/sign-sessions';
import { ConferenceViews } from '@/types/conferences';
import { Api } from '@/modules/api/Api';
import { LogicError } from '@/modules/errors/LogicError';
import { safeMapGetter } from '@/modules/lib';
import { safeExecute } from '@/modules/api/wrappers';

export type ViewName = SignSessionViews | ConferenceViews

type SetViewCounterPayload = {
   viewName: ViewName
   counter: number | string
}

export type FetchViewPayload = {
   viewName: ViewName
   page?: string
}

export type NavigationState = {
   avoidRouteLeaveGuard: boolean
   viewCounters: Map<ViewName, number | string | null>
   selectedView: ViewName
   views: Map<ViewName, unknown>
}

type Context = ActionContext<NavigationState, RootState>

export default {
   namespaced: true,
   state: (): NavigationState => ({
      avoidRouteLeaveGuard: false,
      viewCounters: new Map(),
      selectedView: SignSessionViews.WaitingAction,
      views: new Map()
   }),
   mutations: {
      setViewCounter(state: NavigationState, { viewName, counter }: SetViewCounterPayload): void {
         state.viewCounters.set(viewName, counter);
      },
      clearViewCounter(state: NavigationState, viewName: ViewName): void {
         state.viewCounters.set(viewName, null);
      }
   },
   actions: {
      async forceChangeRoute({ state }: Context, route: RouteLocationRaw): Promise<void> {
         state.avoidRouteLeaveGuard = true;
         await safeExecute(router.push(route));
         state.avoidRouteLeaveGuard = false;
      },
      changeSelectedView({ state }: Context, viewName: ViewName): void {
         state.selectedView = viewName;
      },
      async fetchView<T>({ state }: Context, { viewName, page }: FetchViewPayload): Promise<void> {
         clearViewCounter(viewName);
         state.views.set(viewName, null);

         let view;
         let counter: number | string;

         switch (viewName) {
         case SignSessionViews.Finished:
         case SignSessionViews.WaitingAction:
         case SignSessionViews.InWork:
            if (!page) {
               throw new LogicError('Отсутствует номер страницы для получения вью');
            }
            view = await Api.getSignSessionsViewItems(viewName, parseInt(page));
            counter = view.pagination.total;
            break;
         case ConferenceViews.MyPlanned:
         case ConferenceViews.MyTodays:
            view = await Api.getConferenceViewItems(viewName);
            counter = view.length;
            break;
         default:
            throw new LogicError('Некорректно указана вью для получения элементов');
         }

         state.views.set(viewName, view);
         setViewCounter(viewName, counter);
      },
      async fetchViewCounter({ state }: Context, viewName: ViewName): Promise<void> {
         clearViewCounter(viewName);
         let getter: Promise<{ count: number }>;

         switch (viewName) {
         case SignSessionViews.Finished:
         case SignSessionViews.WaitingAction:
         case SignSessionViews.InWork:
            getter = Api.getSignSessionsViewCounter(viewName);
            break;
         case ConferenceViews.MyPlanned:
         case ConferenceViews.MyTodays:
            getter = Api.getConferenceViewCounter(viewName);
            break;
         default:
            throw new LogicError('Некорректно указана вью для получения счетчика');
         }

         getter
            .then(response => setViewCounter(viewName, response.count))
            .catch(() => setViewCounter(viewName, '-'));
      }

   },
   getters: {
      getView<T>(state: NavigationState): (viewName: ViewName) => T {
         return (viewName) => safeMapGetter(state.views, viewName) as T;
      },
      getViewCounter(state: NavigationState): (viewName: ViewName) => number | string | null {
         return (viewName) => state.viewCounters.get(viewName) ?? null;
      }
   }
};

const setViewCounter = (viewName: ViewName, counter: number | string): void => {
   store.commit('navigation/setViewCounter', { viewName, counter });
};

const clearViewCounter = (viewName: ViewName): void => {
   store.commit('navigation/clearViewCounter', viewName);
};
