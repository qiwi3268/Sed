import { RouteLocationRaw, RouteRecordRaw } from 'vue-router';
import router, { Routes } from '@/router/index';
import store from '@/store';
import { Toast } from '@/modules/notification/Toast';

export const getVacationRoutes = (): RouteRecordRaw[] => {
   return [
      {
         path: 'vacations',
         name: Routes.Vacations,
         component: () => import(/* webpackChunkName: "Vacations" */ '@/pages/vacations/VacationList.vue'),
         beforeEnter: (to, from, next) => {
            store.dispatch('vacations/checkCanManageVacations');
            store.dispatch('vacations/fetchUserOnVacationPercent');
            store.dispatch('vacations/getForNext30DaysVacations');
            store.dispatch('vacations/fetchUsersOnVacation');
            next();
         }
      },
      {
         path: 'vacations/management',
         name: Routes.VacationsManagement,
         component: () => import(/* webpackChunkName: "VacationsEdit" */ '@/pages/vacations/VacationManagement.vue'),
         beforeEnter: async(to, from, next) => {
            const canManageVacations = await store.dispatch('vacations/checkCanManageVacations');
            if (canManageVacations) {
               try {
                  await store.dispatch('vacations/getNextVacations');
                  next();
               } catch (exc) {
                  next(from);
               }
            } else {
               Toast.noAccessToManageVacations();
               next(from);
            }
         }
      }
   ];
};

export const returnToVacationList = (): void => {
   router.push(getVacationNavigationRoute());
};

export const getVacationNavigationRoute = (): RouteLocationRaw => {
   return { name: Routes.Vacations };
};

