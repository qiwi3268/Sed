<template>
<div v-if="value.length > 0" class="form-field form-field--filled">
   <div class="form-field__label">{{ label }}</div>
   <div class="form-field__list">
      <template v-if="value.length > 0">
         <div v-for="item in value" :key="item" class="form-field__row form-field__list-item">
            <i class="pi pi-circle-on form-field__list-point"></i>
            <div class="form-field__value">{{ item }}</div>
         </div>
      </template>
      <div v-else class="form-field__row">Не выбрано</div>
   </div>
</div>
</template>

<script setup lang="ts">
const props = defineProps<{
   label: string
   value: string[]
}>();

</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.form-field {
   &__list {
      @extend %field;

      flex-direction: column;
      align-items: stretch;
      color: $text;

      padding: 10px;
   }

   &__list-point {
      @extend %list-point;
   }

   &__list-item {
      display: flex;
      align-items: center;
   }

   &__row {
      &:not(:last-child) {
         margin: 0 0 5px 0;
      }
   }
}
</style>
