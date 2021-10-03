<template>
<transition name="fade">
   <div v-if="opened">
      <div @click="$emit('close')" class="overlay"></div>
      <div class="modal validation-result-modal">
         <ValidationResultComponent
            v-for="(result, index) in validationResult"
            :key="index"
            :validationResult="result"
         />
      </div>
   </div>
</transition>
</template>

<script setup lang="ts">
import ValidationResultComponent from '@/components/modals/files/signer/ValidationResult.vue';
import { ValidationResult } from '@/modules/api/handlers/files/ExternalSignValidation';

const props = defineProps<{
   validationResult?: ValidationResult[]
   opened: boolean
}>();
</script>

<style lang="scss">
@use 'resources/scss/abstract' as *;

.validation-result-modal {
   .validation-result__sign {
      &:not(&:last-child) {
         border-bottom: 3px solid $blue;
      }
   }
}
</style>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/modals/modal';

.validation-result-modal {
   height: 500px;
   width: 800px;

   overflow-x: hidden;
   overflow-y: auto;

   @media screen and (max-width: $lg) {
      width: 550px;
      height: 450px;
      font-size: 0.875rem;
   }

   @media screen and (max-width: $md) {
      height: 350px;
      width: 300px;
      font-size: 0.75rem;
   }
}

</style>
