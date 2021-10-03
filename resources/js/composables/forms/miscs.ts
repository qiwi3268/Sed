import store from '@/store';
import { MiscItem } from '@/store/modules/misc';
import { computed, onMounted, watch } from 'vue';
import { FormUnit } from '@/modules/forms/validators';
import { ComputedRef } from '@vue/reactivity';

type MiscsModule = {
   fetchMisc: () => void
   getMisc: ComputedRef<MiscItem[]>
}

export const useSingleMiscs = (unit: FormUnit<MiscItem>, miscAlias: string): MiscsModule => {
   /**
    * Вызывает событие получения с апи элементов справочника
    */
   const fetchMisc = (): void => { store.dispatch('misc/fetchSingleMisc', miscAlias); };

   onMounted(() => {
      if (unit.$model) {
         store.dispatch('misc/setMiscItem', { miscAlias, item: unit.$model });
      }
   });

   const getMisc = computed(() => store.getters['misc/getSingleMiscItems'](miscAlias));
   watch(
      () => store.getters['misc/isMiscFetched'](miscAlias),
      () => {
         if (unit.$model) {
            unit.$model = getMisc.value.find(user => user.id === unit.$model.id);
         }
      },
      { immediate: false }
   );

   return {
      fetchMisc,
      getMisc
   };
};
