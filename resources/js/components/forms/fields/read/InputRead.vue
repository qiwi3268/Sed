<template>
<div v-if="value" class="form-field form-field--filled">
   <div class="form-field__label">{{ label }}</div>
   <div class="form-field__row">{{ value || 'Не заполнено' }}</div>
</div>
</template>

<script setup lang="ts">
const props = defineProps<{
   label: string
   value?: string
}>();
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.form-field__row {
   @extend %field;
   padding: 10px;
}
</style>
