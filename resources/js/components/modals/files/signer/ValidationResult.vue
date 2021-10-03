<template>
<div class="validation-result__sign">
   <div class="validation-result__sign-row validation-result__sign-row--status">
      <div class="validation-result__label">Сертификат:</div>
      <div class="validation-result__text" :class="validationResult.certificateResult ? 'valid' : 'invalid'">{{ validationResult.certificateMessage }}</div>
   </div>
   <div class="validation-result__sign-row validation-result__sign-row--status">
      <div class="validation-result__label">Подпись:</div>
      <div class="validation-result__text" :class="validationResult.signatureResult ? 'valid' : 'invalid'">{{ validationResult.signatureMessage }}</div>
   </div>
   <div class="validation-result__sign-row">
      <div class="validation-result__label">Подписант:</div>
      <div class="validation-result__text">{{ validationResult.fio }}</div>
   </div>
   <div>
      <div class="validation-result__sign-row">
         <div class="validation-result__label">Серийный номер:</div>
         <div class="validation-result__text">{{ validationResult.certificate.serial }}</div>
      </div>
      <div class="validation-result__sign-row">
         <div class="validation-result__label">Издатель:</div>
         <div class="validation-result__text">{{ validationResult.certificate.issuer }}</div>
      </div>
      <div class="validation-result__sign-row">
         <div class="validation-result__label">Срок действия:</div>
         <div class="validation-result__text">{{ validationResult.certificate.validRange }}</div>
      </div>
   </div>
</div>
</template>

<script setup lang="ts">
import { ValidationResult } from '@/modules/api/handlers/files/ExternalSignValidation';

const props = defineProps<{
   validationResult: ValidationResult
}>();
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.validation-result {
   &__sign {
      padding: 20px;
   }

   &__sign-row {
      line-height: 1.5;

      display: flex;
      flex-direction: column;
      align-items: flex-start;

      &:not(&:last-child) {
         margin: 0 0 20px 0;
         padding: 0 0 20px 0;
         border-bottom: 1px solid $border;
      }

      &--status {
         .validation-result__text {
            border-radius: 3px;
            cursor: default;
            padding: 5px;
            font-weight: 300;
            font-style: italic;
            transition: background-color 0.1s;

            color: $error;
            border: 1px solid $error;
            background-color: #FAF4F4;

            &.valid {
               color: $valid-text;
               border-color: $valid-text;
               background-color: #F5FAF6;
            }
         }
      }
   }

   &__label {
      font-weight: 400;
      font-size: 1.125rem;
      margin: 0 0 7px 0;
   }
}

</style>
