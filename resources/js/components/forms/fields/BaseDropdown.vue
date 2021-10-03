<template>
<div class="form-field"
     :class="{ invalid: unit.$error, required: unit.mRequired || unit.required }"
>
   <div class="form-field__label">{{ label }}</div>
   <div class="form-field__body">
      <Dropdown
         v-model="unit.$model"
         :options="getMisc"
         optionLabel="label"
         :filter="filter"
         :filterFields="['label']"
         :showClear="true"
         scrollHeight="500px"
         @beforeShow.once="fetchMisc"
         @change="$emit('select')"
         placeholder="Выберите значение"
         class="form-field__dropdown"
      >
         <template #empty>
            <ProgressSpinner style="width:50px;height:50px"  animationDuration=".8s"/>
         </template>
      </Dropdown>
      <div v-if="unit.$error" class="form-field__error">{{ unit.$errors[0].$message }}</div>
   </div>
</div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import { FormUnit } from '@/modules/forms/validators';
import Dropdown from 'primevue/dropdown';
import ProgressSpinner from 'primevue/progressspinner';
import { useSingleMiscs } from '@/composables/forms/miscs';
import { MiscItem } from '@/store/modules/misc';

const props = defineProps<{
   label: string
   field: FormUnit<MiscItem>
   alias: string
   filter?: boolean
}>();

const unit = reactive(props.field);
const { getMisc, fetchMisc } = useSingleMiscs(unit, props.alias);
</script>

<style scoped lang="scss">
</style>
