import { RouteRecordRaw } from 'vue-router';
import { Routes } from '@/router/index';

export const getPollRoutes = (): RouteRecordRaw[] => {
   return [
      {
         path: 'polls/atWork',
         name: Routes.PollsAtWork,
         component: () => import(/* webpackChunkName: "PollsAtWork" */ '@/pages/polls/views/PollsAtWork.vue')
      }
   ];
};


