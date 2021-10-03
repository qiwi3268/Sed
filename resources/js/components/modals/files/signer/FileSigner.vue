<template>
<div @click="close" class="overlay"></div>
<div class="modal sign-modal">
   <FontAwesomeIcon @click="close" size="4x" class="modal-close" icon="times"/>

   <div class="sign-modal__top">
      <PluginInfo v-if="creatingSign && plugin.isInitialized" :plugin="plugin"/>

      <div class="sign-modal__file-body">
         <div class="modal-file">
            <FontAwesomeIcon size="lg" class="modal-file__icon" :icon="getFileIcon(file.originalName)"/>
            <div class="modal-file__info">
               <div class="modal-file__name">{{ file.originalName }}</div>
               <div class="modal-file__size">{{ file.sizeString }}</div>
            </div>
         </div>

         <div class="sign-modal__buttons">
            <template v-if="!signed && !creatingSign">
               <Button @click="selectSign" label="Загрузить подпись" class="sign-modal__button p-button-outlined" />
               <Button @click="enableSignCreating" label="Создать подпись" class="sign-modal__button p-button-outlined" />
            </template>
            <input @change="sendSign" type="file" name="downloadFiles[]" ref="selectFileInput" hidden/>
         </div>
      </div>
      <template v-if="signed">
         <template v-for="(validationResult, index) in validationResults" :key="index">
            <ValidationResultComponent :validationResult="validationResult"/>
         </template>
      </template>
   </div>
   <div v-if="!signed && creatingSign" class="sign-modal__bottom">
      <template v-if="plugin.isInitialized">
         <div class="sign-modal__cert-list">
            <div class="sign-modal__title">Выберите сертификат:</div>
            <template v-for="cert in certs" :key="cert.thumb">
               <div
                  @click="selectCert(cert)"
                  class="sign-modal__cert"
                  :class="{
                     selected: selectedCert?.thumb === cert.thumb,
                     invalid: selectedCert?.thumb === cert.thumb && !selectedCertInfo?.certStatus }"
               >{{ cert.text }}</div>
            </template>
         </div>
         <SelectedCertInfo v-if="selectedCertInfo" :certInfo="selectedCertInfo"/>
      </template>

      <div class="sign-modal__actions modal-actions">
         <div @click="createSign" class="modal-actions__button modal-actions__button--signing">
            <div class="modal-actions__spinner">
               <ProgressSpinner v-if="isSigning" style="width:20px;height:20px" animationDuration=".8s"/>
            </div>
            <div class="modal-actions__label">Подписать</div>
            <div class="modal-actions__empty"></div>
         </div>
         <div @click="disableSignCreating" class="modal-actions__button">Отмена</div>
      </div>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import store from '@/store';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Cert, CspPlugin } from '@/store/modules/modals/files/signer';
import { getFileIcon } from '@/modules/modals/files/utils';
import { GeFile } from '@/store/modules/modals/files/uploader';
import SelectedCertInfo from '@/components/modals/files/signer/SelectedCertInfo.vue';
import PluginInfo from '@/components/modals/files/signer/PluginInfo.vue';
import ValidationResultComponent from '@/components/modals/files/signer/ValidationResult.vue';
import { ValidationResult } from '@/modules/api/handlers/files/ExternalSignValidation';
import ProgressSpinner from 'primevue/progressspinner';
import Button from 'primevue/button';
import { Toast } from '@/modules/notification/Toast';

const file = computed<GeFile>(() => store.getters['fileSigner/getFile']);
const signed = computed(() => file.value.validationResult.length > 0);
const validationResults = computed<ValidationResult[]>(() => file.value.validationResult);

const close = (): void => {
   if (!isSigning.value) {
      store.dispatch('fileSigner/close');
   }
};

const plugin = computed<CspPlugin>(() => store.state.fileSigner.plugin);
const creatingSign = ref(false);
const enableSignCreating = () => {
   if (!plugin.value.isInitialized) {
      store.dispatch('fileSigner/initializePlugin')
         .then(() => creatingSign.value = true);
   } else {
      creatingSign.value = true;
   }
};
const disableSignCreating = () => { creatingSign.value = false; };

const certs = computed<Cert[]>(() => store.state.fileSigner.certs);
const selectCert = (cert: Cert): void => { store.dispatch('fileSigner/selectCert', cert); };
const selectedCert = computed(() => store.state.fileSigner.selectedCert);
const selectedCertInfo = computed(() => store.state.fileSigner.selectedCertInfo);

const opened = computed(() => store.state.fileSigner.opened);
const isSigning = computed(() => store.state.fileSigner.isSigning);
const createSign = () => {
   if (!selectedCert.value) {
      Toast.certIsNotSelected();
   } else if (!isSigning.value && opened.value) {
      store.dispatch('fileSigner/createSign')
         .then(() => close());
   }
};

const selectFileInput = ref();
const selectSign = (): void => {
   if (!isSigning.value && selectFileInput.value) {
      selectFileInput.value.value = '';
      selectFileInput.value.click();
   }
};
const sendSign = () => {
   const fileList: FileList | null = selectFileInput.value.files;
   store.dispatch('fileSigner/uploadExternalSignFiles', fileList)
      .then(() => close());
};
</script>

<style scoped lang="scss">
@use 'resources/scss/elements/form-button';
@use 'resources/scss/modals/sign';
</style>
