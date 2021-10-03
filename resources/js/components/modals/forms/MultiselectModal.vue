<template>
<Teleport to="#app">
   <transition name="fade">
      <div v-if="opened">
         <div @click="close" class="overlay"></div>
         <div class="modal misc-modal">
            <FontAwesomeIcon @click="close" size="4x" class="modal-close" icon="times"/>
            <div class="misc-modal__wrapper">
               <div class="misc-modal__search search">
                  <div class="search__body">
                     <FontAwesomeIcon class="search__icon" icon="search"/>
                     <input
                        v-model="filter"
                        class="search__input"
                        type="text"
                        placeholder="Поиск в справочнике"
                        ref="searchInput"
                     >
                  </div>
               </div>
               <div class="misc-modal__body">
                  <div v-if="selectedItems.length > 0" class="misc-modal__selected-list">
                     <div v-for="item in selectedItems" :key="item.id" class="misc-modal__selected-item">
                        {{ item.label }}
                     </div>
                  </div>
                  <div v-if="items.length === 0" class="misc-modal__spinner">
                     <ProgressSpinner style="width:40px;height:40px" animationDuration=".8s"/>
                  </div>
                  <div v-else-if="filteredItems.length === 0" class="misc-modal__empty">Результаты не найдены</div>
                  <div class="misc-modal__items" v-else>
                     <div
                        v-for="item in filteredItems"
                        :key="item.id"
                        @click="selectItem(item)"
                        class="misc-modal__item p-ripple p-ripple--blue"
                        :class="{ selected: selectedItems.find(selectedItem => selectedItem.id === item.id) }"
                        v-ripple
                     >
                        <Checkbox name="items" :value="item" :modelValue="selectedItems" class="misc-modal__checkbox"/>
                        <div class="misc-modal__label">{{ item.label }}</div>
                     </div>
                  </div>
               </div>
               <div class="misc-modal__actions modal-actions">
                  <div @click="close" class="modal-actions__button">Подтвердить</div>
                  <div @click="unselectAll" class="modal-actions__button">Снять выбор</div>
               </div>
            </div>
         </div>
      </div>
   </transition>
</Teleport>

</template>

<script lang="ts">
import { computed, defineComponent, nextTick, PropType, reactive, ref, watch } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { MiscItem } from '@/store/modules/misc';
import Checkbox from 'primevue/checkbox';
import ProgressSpinner from 'primevue/progressspinner';

export default defineComponent({
   name: 'MultiselectModal',
   components: {
      FontAwesomeIcon,
      Checkbox,
      ProgressSpinner
   },
   props: {
      items: {
         type: Object as PropType<MiscItem[]>,
         required: true
      },
      model: {
         type: Object as PropType<MiscItem[]>,
         required: true
      },
      opened: {
         type: Boolean,
         required: true
      }
   },
   emits: {
      close: null
   },
   setup(props, { emit }) {
      const selectedItems = computed(() => props.model);

      const close = () => {
         emit('close');
         clearFilter();
      };

      const searchInput = ref<HTMLInputElement | null>(null);
      watch(
         () => props.opened,
         () => {
            if (props.opened) {
               nextTick(() => searchInput.value!.focus());
            }
         },
         { immediate: false }
      );

      const selectItem = (item: MiscItem) => {
         filter.value = '';
         const index = selectedItems.value.findIndex(selectedItem => selectedItem.id === item.id);
         if (index !== -1) {
            selectedItems.value.splice(index, 1);
         } else {
            selectedItems.value.push(item);
         }
      };
      const unselectAll = () => selectedItems.value.splice(0, selectedItems.value.length);

      const filter = ref('');
      const filteredItems = computed(() => {
         return filter.value
            ? props.items.filter(item => item.label.toLowerCase().includes(filter.value.toLowerCase()))
            : props.items;
      });
      const clearFilter = () => filter.value = '';

      return {
         unselectAll,
         selectedItems,
         selectItem,
         filter,
         filteredItems,
         close,
         searchInput
      };
   }
});
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/modals/modal';

.wrapper {
   position: absolute;
}

.misc-modal {
   min-width: 500px;
   width: unset;
   border-radius: 4px;
   position: fixed;
   top: 80px;
   left: 50%;
   transform: translate(-50%, 0);

   @media screen and (max-width: $md) {
      top: 60px;
   }

   @media screen and (max-width: $sm) {
      top: 40px;
      min-width: 300px;
      font-size: 0.875rem;
   }

   &__search {
      border-bottom: 1px solid $border;
   }

   &__wrapper {
      box-shadow: 0 3px 10px -.5px rgba(0, 0, 0, .2);
      background-color: #fff;
      overflow: hidden;
      border-radius: 4px;
   }

   &__left {
      border-right: 1px solid $border;
   }

   &__selected-list {
      padding: 10px 15px;
      border-right: 1px solid $border;

      max-height: 500px;
      overflow-x: hidden;
      overflow-y: auto;
      flex-grow: 1;

      @media screen and (max-width: $sm) {
         padding: 5px 10px;
         border-right: unset;
         border-bottom: 1px solid $border;

         max-height: 200px;
      }
   }

   &__selected-item {
      &:not(&:last-child) {
         margin: 0 0 12px 0;

         @media screen and (max-width: $md) {
            margin: 0 0 7px 0;
         }
      }
   }

   &__body {
      display: flex;

      @media screen and (max-width: $sm) {
         flex-direction: column;
      }
   }

   &__items {
      max-height: 500px;
      overflow-x: hidden;
      overflow-y: auto;
      flex-grow: 1;

      @media screen and (max-width: $sm) {
         max-height: 300px;
      }
   }

   &__item {
      cursor: pointer;
      padding: 15px 25px;
      transition: background-color 0.15s;

      display: flex;
      align-items: center;

      &.selected {
         background-color: #F1F6F9;
      }

      &:hover {
         background-color: $background;
      }

      @media screen and (max-width: $md) {
         padding: 10px;
      }
   }

   &__checkbox {
      margin: 0 15px 0 0;

      @media screen and (max-width: $md) {
         margin: 0 7px 0 0;
      }
   }

   &__empty {
      padding: 15px 25px;
      font-size: 1.125rem;
   }

   &__actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
   }

   &__spinner {
      margin: 10px;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-grow: 1;
   }

}

.search {
   padding: 10px 15px;
   display: flex;
   align-items: center;
   font-family: 'Roboto Condensed', sans-serif;

   @media screen and (max-width: $md) {
      padding: 7px 10px;
   }

   &__body {
      display: flex;
      align-items: center;
      flex-grow: 1;
   }

   &__icon {
      margin: 0 5px 0 0;
      color: $icon-blue;
      font-size: 1.125rem;
   }

   &__input {
      padding: 5px;
      font-size: 1rem;
      flex-grow: 1;
   }
}
</style>
