import { onBeforeRouteLeave } from 'vue-router';
import store from '@/store';
import { confirmLeaving } from '@/modules/modals/Confirm';

export const useRouteLeaveGuard = (): void => {
   onBeforeRouteLeave((to, from, next) => {
      store.state.navigation.avoidRouteLeaveGuard ? next() : confirmLeaving(next);
   });
};
