<template>
<div class="session-read">
   <div class="session-read__block">
      <div class="session-read__row row-form">
         <FileFieldRead label="Файл" :files="[model.file]" :showSign="false">
            <template #label>
               <div class="form-field__action">
                  <div class="form-field__action-label">Файл</div>
                  <Button @click="downloadSigns" label="Скачать архив с подписями" class="p-button-outlined"/>
               </div>
            </template>
         </FileFieldRead>
      </div>
   </div>
   <div class="session-read__section">
      <div class="session-read__card">
         <div class="session-read__header">Подписано</div>
         <div v-if="model.signers.signed.length > 0" class="session-read__body session-signers">
            <div v-for="(signer, index) in model.signers.signed" :key="index" class="session-signers__item">
               <FontAwesomeIcon icon="check-circle" class="session-signers__icon session-signers__icon--valid"/>
               <div class="session-signers__fio">{{ signer.fio }}</div>
               <div class="session-signers__info">
                  <div
                     v-if="signer.signerState !== SignerStates.Valid"
                     @click="showValidationResult(signer.validationResult.signers)"
                     v-tooltip="'Подписант не совпадает'"
                     class="session-signers__warning"
                  >
                     <FontAwesomeIcon icon="exclamation"/>
                  </div>
                  <div
                     @click="showValidationResult(signer.validationResult.signers)"
                     class="session-signers__date"
                  >{{ signer.signedAt }}</div>
               </div>
            </div>
         </div>
         <div v-else class="session-read__empty">Список пуст</div>
      </div>
      <div class="session-read__card">
         <div class="session-read__header">Не подписано</div>
         <div v-if="model.signers.unsigned.length > 0" class="session-read__body session-signers">
            <div v-for="(signer, index) in model.signers.unsigned" :key="index" class="session-signers__item">
               <FontAwesomeIcon icon="clock" class="session-signers__icon session-signers__icon--waiting"/>
               <div class="session-signers__fio">{{ signer.fio }}</div>
               <div class="session-signers__status">{{ signer.statusName }}</div>
            </div>
         </div>
         <div v-else class="session-read__empty session-read__empty--success">Список пуст</div>
      </div>
   </div>
</div>
<ValidationResultModal
   :validationResult="validationResult"
   :opened="openedValidationResult"
   @close="openedValidationResult = false"
/>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import FileFieldRead from '@/components/forms/fields/read/FileFieldRead.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import Button from 'primevue/button';
import store from '@/store';
import { ValidationResult } from '@/modules/api/handlers/files/ExternalSignValidation';
import ValidationResultModal from '@/components/modals/files/ValidationResultModal.vue';
import { SignerStates, SignSessionRead } from '@/types/sign-sessions';

const props = defineProps<{
   model: SignSessionRead
}>();

const downloadSigns = () => store.dispatch('signSessions/downloadSigns', props.model);

const openedValidationResult = ref(false);
const validationResult = ref();
const showValidationResult = (signs: ValidationResult[]) => {
   openedValidationResult.value = true;
   validationResult.value = signs;
};
</script>

<style lang="scss">
@use 'resources/scss/forms/row-form';
@use 'resources/scss/sign-sessions/session-read';
@use 'resources/scss/sign-sessions/session-signers';
</style>
