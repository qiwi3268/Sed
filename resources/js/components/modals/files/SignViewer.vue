<template>
<div @click="close" class="overlay"></div>
<div class="modal sign-modal">
   <FontAwesomeIcon @click="close" size="4x" class="modal-close" icon="times"/>
   <div class="sign-modal__top">
      <div class="sign-modal__file-body">
         <div class="modal-file">
            <FontAwesomeIcon size="lg" class="modal-file__icon" :icon="getFileIcon(file.originalName)"/>
            <div class="modal-file__info">
               <div class="modal-file__name">{{ file.originalName }}</div>
               <div class="modal-file__size">{{ file.sizeString }}</div>
            </div>
         </div>
      </div>
      <template v-for="(validationResult, index) in validationResults" :key="index">
         <ValidationResultComponent :validationResult="validationResult"/>
      </template>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import store from '@/store';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { getFileIcon } from '@/modules/modals/files/utils';
import { ValidationResult } from '@/modules/api/handlers/files/ExternalSignValidation';
import { GeFile } from '@/store/modules/modals/files/uploader';
import ValidationResultComponent from '@/components/modals/files/signer/ValidationResult.vue';

const file = computed<GeFile>(() => store.getters['signViewer/getFile']);
const validationResults = computed<ValidationResult[]>(() => file.value.validationResult);

const close = (): void => { store.dispatch('signViewer/close'); };
</script>

<style scoped lang="scss">
@use 'resources/scss/modals/sign';

</style>
