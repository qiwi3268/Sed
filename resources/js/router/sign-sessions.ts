import { RouteLocationRaw, RouteRecordRaw } from 'vue-router';
import store from '@/store';
import { Api } from '@/modules/api/Api';
import router, { NavigationGuardPayload, Routes } from '@/router/index';
import { Toast } from '@/modules/notification/Toast';
import { SignSessionViews } from '@/types/sign-sessions';
import { actionGuard } from '@/modules/api/wrappers';

export const getSignSessionRoutes = (): RouteRecordRaw[] => {
   return [
      {
         path: 'signatureSession/create',
         name: Routes.SignSessionCreate,
         component: () => import(/* webpackChunkName: "SignSessionsCreate" */ '@/pages/sign-sessions/SignSessionCreate.vue')
      },
      {
         path: 'signatureSession/:signatureSessionUuid',
         name: Routes.SignSessionRead,
         component: () => import(/* webpackChunkName: "SignSessionsRead" */ '@/pages/sign-sessions/SignSessionRead.vue'),
         beforeEnter: async(to, from, next) => {
            try {
               await store.dispatch('signSessions/fetchSignSessionRead', to.params.signatureSessionUuid);
            } catch {
               returnToSignSessionNavigation();
            }
            next();
         }
      },
      {
         path: 'signatureSession/:signatureSessionUuid/signing',
         name: Routes.SignSessionSigning,
         component: () => import(/* webpackChunkName: "SignSessionsSigning" */ '@/pages/sign-sessions/SignSessionSigning.vue'),
         beforeEnter: async(to, from, next) => {
            const sessionId = to.params.signatureSessionUuid as string;
            const canSign = await actionGuard(Api.canUserSignSignSession(sessionId));
            if (canSign) {
               try {
                  await store.dispatch('signSessions/fetchSignSessionSigning', sessionId);
                  next();
               } catch {
                  next(from);
               }
            } else {
               Toast.noAccessToSignSession();
               next(from);
            }
         }
      },
      {
         path: 'signatureSession/navigation',
         name: Routes.SignSessionNavigation,
         component: () => import(/* webpackChunkName: "SignSessionNavigation" */ '@/pages/sign-sessions/SignSessionNavigation.vue'),
         redirect: {
            name: SignSessionViews.WaitingAction
         },
         children: [
            {
               path: SignSessionViews.WaitingAction,
               name: SignSessionViews.WaitingAction,
               component: () => import(/* webpackChunkName: "SignSessionsWaitingAction" */ '@/pages/sign-sessions/views/SignSessionsWaitingAction.vue'),
               beforeEnter: (to, from, next) => {
                  beforeEnterInView(SignSessionViews.WaitingAction, { to, from, next });
               }
            },
            {
               path: SignSessionViews.InWork,
               name: SignSessionViews.InWork,
               component: () => import(/* webpackChunkName: "SignSessionsInWork" */ '@/pages/sign-sessions/views/SignSessionsInWork.vue'),
               beforeEnter: (to, from, next) => {
                  beforeEnterInView(SignSessionViews.InWork, { to, from, next });
               }
            },
            {
               path: SignSessionViews.Finished,
               name: SignSessionViews.Finished,
               component: () => import(/* webpackChunkName: "SignSessionsFinished" */ '@/pages/sign-sessions/views/SignSessionsFinished.vue'),
               beforeEnter: (to, from, next) => {
                  beforeEnterInView(SignSessionViews.Finished, { to, from, next });
               }

            }
         ]
      }
   ];
};

const beforeEnterInView = (viewName: SignSessionViews, { to, next }: NavigationGuardPayload): void => {
   if (Array.isArray(to.query.page) || !to.query.page || isNaN(parseInt(to.query.page))) {
      to.query.page = '1';
      next(to);
   } else {
      store.dispatch('navigation/changeSelectedView', viewName);

      next();
      const page = to.query.page;
      store.dispatch('navigation/fetchView', { viewName, page });
   }
};

export const returnToSignSessionNavigation = (): void => {
   router.push(getSignSessionNavigationRoute());
};

export const getSignSessionNavigationRoute = (): RouteLocationRaw => {
   return {
      name: Routes.SignSessionNavigation,
      query: { page: 1 }
   };
};

export const getSignSessionReadRoute = (signatureSessionUuid: string): RouteLocationRaw => {
   return {
      name: Routes.SignSessionRead,
      params: { signatureSessionUuid }
   };
};

export const getSignSessionSigningRoute = (signatureSessionUuid: string): RouteLocationRaw => {
   return {
      name: Routes.SignSessionSigning,
      params: { signatureSessionUuid }
   };
};
