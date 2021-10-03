<template>
<div v-if="value" class="form-field form-field--filled">
   <div class="form-field__label">{{ label }}</div>
   <div class="form-field__row">{{ date || 'Не выбрано' }}</div>
</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { formatDate, formatDateTime } from '@/modules/forms/formatting';

const props = defineProps<{
   label: string
   value?: Date
   showTime?: boolean
}>();

const date = computed(() => {
   let result: string | null = null;

   if (props.value) {
      result = props.showTime ? formatDateTime(props.value) : formatDate(props.value);
   }

   return result;
});
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.form-field__row {
   @extend %field;
   padding: 10px;
}
</style>
