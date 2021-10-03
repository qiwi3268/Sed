import {
   createRouter,
   createWebHistory,
   NavigationGuardNext,
   RouteLocationNormalized, RouteLocationRaw,
   RouteRecordRaw
} from 'vue-router';
import {
   getSignSessionNavigationRoute,
   getSignSessionRoutes
} from '@/router/sign-sessions';
import { getPollRoutes } from '@/router/polls';
import { getVacationRoutes } from '@/router/vacations';
import { getConferenceRoutes } from '@/router/conferences';
import store from '@/store';
import { getBirthdaysRoutes } from '@/router/birthdays';

export enum Routes {
   Root = 'Root',
   Navigation = 'Navigation',
   Home = 'Home',
   Login = 'Login',
   NotFound = 'NotFound',

   SignSessionCreate = 'SignSessionCreate',
   SignSessionRead = 'SignSessionRead',
   SignSessionSigning = 'SignSessionSigning',
   SignSessionNavigation = 'SignSessionNavigation',

   PollsAtWork = 'PollsAtWork',

   Vacations = 'Vacations',
   VacationsManagement = 'VacationsManagement',

   ConferenceCreate = 'ConferenceCreate',
   ConferenceRead = 'ConferenceRead',
   ConferenceUpdate = 'ConferenceUpdate',
   ConferenceNavigation = 'ConferenceNavigation',
   ConferenceItems = 'ConferenceItems',

   Birthdays = 'Birthdays'
}

const routes: Array<RouteRecordRaw> = [
   {
      path: '/',
      name: Routes.Root,
      redirect: { name: Routes.Home }
   },
   {
      path: '/home',
      name: Routes.Home,
      component: () => import(/* webpackChunkName: "Home" */ '@/pages/Home.vue'),
      meta: {
         auth: true
      },
      redirect: {
         name: Routes.SignSessionNavigation
      },
      children: [
         ...getSignSessionRoutes(),
         ...getPollRoutes(),
         ...getVacationRoutes(),
         ...getConferenceRoutes(),
         ...getBirthdaysRoutes()
      ]
   },
   {
      path: '/login',
      name: Routes.Login,
      component: () => import(/* webpackChunkName: "Login" */ '@/pages/Login.vue')
   },
   {
      path: '/:pathMatch(.*)*',
      name: Routes.NotFound,
      component: () => import(/* webpackChunkName: "NotFound" */ '@/pages/NotFound.vue'),
      meta: {
         auth: true
      }
   }

];

const router = createRouter({
   history: createWebHistory(process.env.BASE_URL),
   routes
});

export const getDefaultNavigationRoute = (): RouteLocationRaw => {
   return getSignSessionNavigationRoute();
};

export const forceChangeRoute = (fallbackRoute: RouteLocationRaw): void => {
   store.dispatch('navigation/forceChangeRoute', fallbackRoute);
};

export type NavigationGuardPayload = {
   to: RouteLocationNormalized
   from: RouteLocationNormalized
   next: NavigationGuardNext
}

export default router;
