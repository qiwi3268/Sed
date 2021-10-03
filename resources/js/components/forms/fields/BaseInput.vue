<template>
<div class="form-field"
     :class="{ invalid: unit.$error, required: unit.mRequired || unit.required }"
>
   <div class="form-field__label">{{ label }}</div>
   <div class="form-field__body">
      <InputText v-model.trim="unit.$model" placeholder="Введите значение"/>
      <div v-if="unit.$error" class="form-field__error">{{ unit.$errors[0].$message }}</div>
   </div>
</div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import InputText from 'primevue/inputtext';
import { FormUnit } from '@/modules/forms/validators';

const props = defineProps<{
   label: string
   field: FormUnit
}>();

const unit = reactive(props.field);
</script>

<style scoped lang="scss">
</style>
