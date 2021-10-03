<template>
<div v-if="value" class="form-field form-field--filled">
   <div class="form-field__label">{{ label }}</div>
   <a
      :href="value"
      @click="(event) => openLink(event, value)"
      class="form-field__row"
   >{{ value || 'Не заполнено' }}</a>
</div>
</template>

<script setup lang="ts">
const props = defineProps<{
   label: string
   value?: string
}>();

const openLink = (event, link) => {
   event.preventDefault();
   if (link) {
      window.open(link);
   }
};
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.form-field__row {
   @extend %field;
   padding: 10px;
}
</style>
