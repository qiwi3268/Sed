import { computed } from 'vue';
import store from '@/store';
import router from '@/router';
import { ComputedRef } from '@vue/reactivity';
import { View } from '@/modules/forms/formatting';
import { SignSessionViews } from '@/types/sign-sessions';

type ViewPaginationModule = {
   changePage: (string) => void
}

/**
 * Добавляет в компонент пагинацию для вью сессий подписания
 *
 * @param viewName - наименование вью
 * @param model - коллекция сессий
 */
export const useViewPagination = (viewName: SignSessionViews, model: ComputedRef<View | null>): ViewPaginationModule => {
   const currentPageNum = computed(() => model.value?.pagination.currentPage.toString() ?? '');
   const changePage = (urlString): void => {
      const url = new URL(urlString);
      const newPageNum = url.searchParams.get('page');

      if (currentPageNum.value !== newPageNum) {
         store.dispatch('navigation/fetchView', { viewName, page: newPageNum })
            .then(() => router.replace({ query: { page: newPageNum } }));
      }
   };

   return {
      changePage
   };
};
