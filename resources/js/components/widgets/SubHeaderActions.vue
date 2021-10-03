<template>
<div class="sub-header__actions">
   <ActionButton
      @click="$emit('ok')"
      label="Сохранить"
      :loading="handlingAction"
      modifier="green"
   />
   <ActionButton @click="$emit('cancel')" label="Выйти"/>
</div>
</template>

<script setup lang="ts">
import ActionButton from '@/components/widgets/ActionButton.vue';

const props = defineProps<{
   handlingAction: boolean
}>();

const emit = defineEmits(['ok', 'cancel']);
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/elements/action-button';

.sub-header {

   &__actions {
      align-self: stretch;
      display: grid;
      grid-template-columns: repeat(2, minmax(120px, 1fr));
      @extend %gcg;

      @media screen and (max-width: $md) {
         grid-template-columns: repeat(2, minmax(60px, 1fr));
      }

      @media screen and (max-width: $sm) {
         grid-template-columns: 1fr 1fr;
      }
   }
}


</style>
