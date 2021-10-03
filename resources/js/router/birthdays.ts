import { RouteRecordRaw } from 'vue-router';
import { Routes } from '@/router/index';
import store from '@/store';

export const getBirthdaysRoutes = (): RouteRecordRaw[] => {
   return [
      {
         path: 'birthdays',
         name: Routes.Birthdays,
         component: () => import(/* webpackChunkName: "BirthDays" */ '@/pages/birthdays/Birthdays.vue'),
         beforeEnter: (to, from, next) => {
            store.dispatch('birthdays/fetchBirthdays');
            next();
         }
      }
   ];
};
