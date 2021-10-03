<template>
<div
   @click="$emit('click')"
   class="view-label"
   :class="{ 'view-label--selected': selected }"
>
   <div class="view-label__block">
      <div class="view-label__label">
         <slot></slot>
      </div>
      <div v-if="hasCounter" class="view-label__value">
         <template v-if="counterFetched">{{ counter }}</template>
         <ProgressSpinner v-else animationDuration=".8s" class="view-label__spinner"/>
      </div>
   </div>
</div>
</template>

<script setup lang="ts">
import ProgressSpinner from 'primevue/progressspinner';

const props = defineProps<{
   selected: boolean
   hasCounter?: boolean
   counterFetched?: boolean
   counter?: number
}>();

const emit = defineEmits<{
   (e: 'click'): void
}>();

</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.view-label {
   padding: 12px 15px;
   font-size: 1.25rem;
   border-left: 3px solid #fff;
   color: $dark-green;
   cursor: pointer;
   transition: border 0.15s, background-color 0.15s, color 0.15s;
   display: flex;
   align-items: center;
   justify-content: center;
   user-select: none;

   $vl: &;

   &--selected {
      background-color: rgba(39, 72, 80, 0.05);
      border-left: 3px solid #517680;
   }

   &:hover {
      border-left: 3px solid $dark-green;
      background-color: $dark-green;
      color: #fff;

      #{$vl}__spinner {
         :deep(.p-progress-spinner-circle) {
            stroke: #fff !important;
         }
      }
   }

   &__label {
      margin: 0 10px 0 0;

      @media screen and (max-width: $md) {
         margin: 0 5px 0 0;
      }
   }

   &__spinner {
      width: 20px;
      height: 20px;

      @media screen and (max-width: $lg) {
         width: 15px;
         height: 15px;
      }

      @media screen and (max-width: $sm) {
         width: 12px;
         height: 12px;
      }
   }

   &__block {
      flex-grow: 1;
      display: flex;
      align-items: center;
      justify-content: space-between;
   }
}
</style>
