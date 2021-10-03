<template>
<div
   class="form-field"
   :class="{ invalid: unit.$error, required: unit.mRequired || unit.required }"
>
   <div class="form-field__label">{{ label }}</div>
   <div class="form-field__body">
      <div
         @click="openModal"
         class="form-field__select field-select"
         :class="{ empty: !hasItems }"
      >
         <div class="field-select__body">
            <template v-if="!hasItems">{{ placeholder }}</template>
            <template v-else>
               <div v-for="item in unit.$model" :key="item.id" class="field-select__chip chip">
                  <div class="chip__label">{{ item.label }}</div>
                  <i @click.stop="removeItem(item)" class="chip__icon pi pi-times-circle"></i>
               </div>
            </template>
         </div>
         <FontAwesomeIcon class="field-select__icon" icon="plus"/>
      </div>
      <div v-if="unit.$error" class="form-field__error">{{ unit.$errors[0].$message }}</div>
   </div>
   <MultiselectModal
      :opened="modalOpened"
      :items="items"
      :model="unit.$model"
      @close="modalOpened = false"
   />
</div>
</template>

<script setup lang="ts">
import { computed, reactive, ref, withDefaults } from 'vue';
import { FormUnit } from '@/modules/forms/validators';
import MultiselectModal from '@/components/modals/forms/MultiselectModal.vue';
import { MiscItem } from '@/store/modules/misc';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = withDefaults(defineProps<{
   label: string
   field: FormUnit<MiscItem[]>
   items: MiscItem[]
   placeholder?: string
}>(), { placeholder: 'Выберите одно или несколько значений' });

const emit = defineEmits(['open']);

const unit = reactive(props.field);

const modalOpened = ref(false);
const openModal = () => {
   emit('open');
   modalOpened.value = true;
};

const hasItems = computed(() => unit.$model.length > 0);

const removeItem = (removedItem: MiscItem) => {
   unit.$model.splice(unit.$model.findIndex(item => item.id === removedItem.id), 1);
};
</script>



<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/modals/modal';

.field-select {
   @extend %field;
   justify-content: space-between;
   padding: 3px 8px;

   &.empty {
      padding: 8px;

      @media screen and (max-width: $md) {
         padding: 7px;
      }

      @media screen and (max-width: $sm) {
         padding: 6px;
      }
   }

   &__icon {
      margin: 0 0 0 5px;
   }
}

.chip {
   margin: 2px;
   padding: 0.25rem 0.5rem;
   background: #dee2e6;
   color: #495057;
   border-radius: 16px;
   display: inline-flex;
   align-items: center;
   cursor: default;

   &__label {
      margin: 0 4px 0 0;
   }

   &__icon {
      cursor: pointer;
   }
}

</style>
