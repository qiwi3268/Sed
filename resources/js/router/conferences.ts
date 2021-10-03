import { NavigationGuardNext, RouteLocationRaw, RouteRecordRaw } from 'vue-router';
import router, { Routes } from '@/router/index';
import store from '@/store';
import { Toast } from '@/modules/notification/Toast';
import { ConferenceViews } from '@/types/conferences';

export const getConferenceRoutes = (): RouteRecordRaw[] => {
   return [
      {
         path: 'conferences/create',
         name: Routes.ConferenceCreate,
         component: () => import(/* webpackChunkName: "ConferenceCreate" */ '@/pages/conferences/ConferenceCreate.vue'),
         beforeEnter: async(to, from, next) => {
            const canCreateConference = await store.dispatch('conferences/checkCanCreateConferences');
            if (canCreateConference) {
               next();
            } else {
               Toast.noAccessToCreateConference();
               next(from);
            }
         }
      },
      {
         path: 'conferences/:conferenceUuid',
         name: Routes.ConferenceRead,
         component: () => import(/* webpackChunkName: "ConferenceRead" */ '@/pages/conferences/ConferenceRead.vue'),
         beforeEnter: (to, from, next) => {
            const conferenceUuid = to.params.conferenceUuid;
            store.dispatch('conferences/fetchConferenceRead', conferenceUuid);
            store.dispatch('conferences/checkCanUserUpdateConference', conferenceUuid);
            store.dispatch('conferences/checkCanUserDeleteConference', conferenceUuid);
            next();
         }
      },
      {
         path: 'conferences/:conferenceUuid/update',
         name: Routes.ConferenceUpdate,
         component: () => import(/* webpackChunkName: "ConferenceEdit" */ '@/pages/conferences/ConferenceEdit.vue'),
         beforeEnter: async(to, from, next) => {
            const conferenceUuid = to.params.conferenceUuid;
            const canUpdate = await store.dispatch('conferences/checkCanUserUpdateConference', conferenceUuid);
            if (canUpdate) {
               await store.dispatch('conferences/fetchConferenceEdit', conferenceUuid);
               next();
            } else {
               Toast.noAccessToUpdateConference();
               next(from);
            }
         }
      },
      {
         path: 'conferences/navigation/items',
         name: Routes.ConferenceNavigation,
         component: () => import(/* webpackChunkName: "ConferenceNavigation" */ '@/pages/conferences/ConferenceNavigation.vue'),
         redirect: {
            name: ConferenceViews.MyTodays
         },
         children: [
            {
               path: ConferenceViews.MyTodays,
               name: ConferenceViews.MyTodays,
               component: () => import(/* webpackChunkName: "ConferenceMyTodays" */ '@/pages/conferences/views/ConferenceMyTodays.vue'),
               beforeEnter: (to, from, next) => {
                  beforeEnterInView(ConferenceViews.MyTodays, next);
               }
            },
            {
               path: ConferenceViews.MyPlanned,
               name: ConferenceViews.MyPlanned,
               component: () => import(/* webpackChunkName: "ConferenceMyPlanned" */ '@/pages/conferences/views/ConferenceMyPlanned.vue'),
               beforeEnter: (to, from, next) => {
                  beforeEnterInView(ConferenceViews.MyPlanned, next);
               }
            },
            {
               path: ConferenceViews.ForDate,
               name: ConferenceViews.ForDate,
               component: () => import(/* webpackChunkName: "ConferenceForDate" */ '@/pages/conferences/ConferenceForDate.vue'),
               beforeEnter: (to, from, next) => {
                  store.dispatch('conferences/setSelectedDate', new Date());
                  store.dispatch('conferences/updateConferencesForDateView');
                  next();
               }
            }

         ]
      }
   ];
};

const beforeEnterInView = (viewName: ConferenceViews, next : NavigationGuardNext): void => {
   store.dispatch('navigation/changeSelectedView', viewName);
   next();
   store.dispatch('navigation/fetchView', { viewName });
};

export const returnToConferenceNavigation = (): void => {
   router.push(getConferenceNavigationRoute());
};

export const getConferenceNavigationRoute = (): RouteLocationRaw => {
   return { name: Routes.ConferenceNavigation };
};

export const getConferenceReadRoute = (conferenceUuid: string): RouteLocationRaw => {
   return { name: Routes.ConferenceRead, params: { conferenceUuid } };
};

export const goToEditConference = async(conferenceUuid: string): Promise<void> => {
   await router.push({ name: Routes.ConferenceUpdate, params: { conferenceUuid } });
};
