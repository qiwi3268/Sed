import { ComputedRef } from '@vue/reactivity';
import { computed, onBeforeUnmount, ref } from 'vue';
import chunk from 'lodash/chunk';
import { View } from '@/modules/forms/formatting';
import { SignSessionViewWindowSizes } from '@/types/sign-sessions';

type ChunkedSignSessions<T> = {
   chunkedSessionList: ComputedRef<T[][]>
}

/**
 * Добавляет в компонент разделение коллекции сессий подписания по чанкам для адаптивного отображения во вью
 *
 * @param model - коллекция сессий
 */
export const useChunkedSignSessionList = <T> (model: ComputedRef<View<T> | null>): ChunkedSignSessions<T> => {
   const chunkedSessionList = computed<T[][]>(() => {
      const sessions = model.value ? model.value.data : [];
      return chunk(sessions, Math.ceil(sessions.length / columnsAmount.value));
   });

   const columnsAmount = ref(3);
   const calculateViewColumnsAmount = () => {
      if (window.innerWidth > SignSessionViewWindowSizes.Md) {
         columnsAmount.value = 3;
      } else if (window.innerWidth > SignSessionViewWindowSizes.Sm) {
         columnsAmount.value = 2;
      } else {
         columnsAmount.value = 1;
      }
   };

   calculateViewColumnsAmount();
   window.addEventListener('resize', calculateViewColumnsAmount);
   onBeforeUnmount(() => {
      window.removeEventListener('resize', calculateViewColumnsAmount);
   });

   return {
      chunkedSessionList
   };
};
