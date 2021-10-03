<template>
<div class="form-field"
     :class="{ invalid: unit.$error, required: unit.mRequired || unit.required }"
>
   <div class="form-field__label">{{ label }}</div>
   <div class="form-field__body">
      <Textarea v-model.trim="unit.$model" :autoResize="true" placeholder="Введите значение"/>
      <div v-if="unit.$error" class="form-field__error">{{ unit.$errors[0].$message }}</div>
   </div>
</div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import Textarea from 'primevue/textarea';
import { FormUnit } from '@/modules/forms/validators';

const props = defineProps<{
   label: string
   field: FormUnit
}>();

const unit = reactive(props.field);
</script>

<style scoped lang="scss">
</style>
