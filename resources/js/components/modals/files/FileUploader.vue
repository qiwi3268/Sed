<template>
<div @click="close" class="overlay"></div>
<div class="modal file-modal">
   <FontAwesomeIcon @click="close" size="4x" class="modal-close" icon="times"/>
   <div class="file-modal__header" >
      <div class="file-modal__title">
         <template v-if="!isUploading">Выберите или перетащите файлы</template>
         <template v-else>
            Загрузка {{ downloadPercent + '%' }}
            <div class="file-modal__progress-bar" :style="{ width: downloadPercent + '%' }"></div>
         </template>
      </div>
   </div>
   <div @drop="dropFiles" class="file-modal__drop-area">
      <div v-for="file in files" :key="file.name" class="file-modal__body">
         <div class="modal-file">
            <FontAwesomeIcon size="lg" class="modal-file__icon" :icon="getFileIcon(file.name)"/>
            <div class="modal-file__info">
               <div class="modal-file__name">{{ file.name }}</div>
               <div class="modal-file__size">{{ getFileSizeString(file.size) }}</div>
            </div>
         </div>
      </div>
   </div>
   <div class="file-modal__actions modal-actions">
      <div @click="select" class="modal-actions__button">Выбрать</div>
      <div @click="upload" class="modal-actions__button">Загрузить</div>
      <div @click="clear" class="modal-actions__button">Удалить файлы</div>
   </div>
   <input @change="addSelectedFiles" ref="selectFileInput" type="file" :multiple="multiple" hidden/>
</div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue';
import store from '@/store';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { getFileIcon, getFileSizeString } from '@/modules/modals/files/utils';
import { clearDefaultDropEvents } from '@/modules/lib';

const opened = computed<boolean>(() => store.state.filesUploader.opened);
const close = (): void => {
   if (!isUploading.value) {
      store.dispatch('filesUploader/close');
   }
};
const multiple = computed(() => store.getters['filesUploader/isMultiple'](store.state.filesUploader.mapping));

onMounted(clearDefaultDropEvents);
const files = ref<File[]>([]);
const dropFiles = (event): void => {
   files.value = [...event.dataTransfer.files];
};
const selectFileInput = ref();
const select = (): void => {
   if (!isUploading.value && opened.value && selectFileInput.value) {
      selectFileInput.value.value = '';
      selectFileInput.value.click();
   }
};
const addSelectedFiles = (): void => {
   const fileList = selectFileInput.value?.files;
   if (fileList) {
      files.value = [...fileList];
   }
};
const clear = (): void => {
   files.value = [];
};

const isUploading = computed(() => store.state.filesUploader.isUploading);
const downloadPercent = ref<number>(0);
const uploadProgressCallback = (event: ProgressEvent): void => {
   downloadPercent.value = Math.round(100 * event.loaded / event.total);
};
const handleUploadingState = (isUploading: boolean): void => {
   if (!isUploading) {
      downloadPercent.value = 0;
      close();
   }
};
watch(isUploading, (isUploading) => handleUploadingState(isUploading), { immediate: false });

const upload = () => {
   if (opened.value) {
      store.dispatch('filesUploader/upload', { files: files.value, uploadProgressCallback });
   }
};

</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/modals/modal';
@use 'resources/scss/elements/modal-file';

.file-modal {
   height: 550px;
   width: 700px;
   display: flex;
   flex-direction: column;

   @media screen and (max-width: $lg) {
      width: 550px;
      height: 450px;
   }

   @media screen and (max-width: $md) {

      height: 350px;
      width: 300px;
   }

   &__drop-area {
      border: 1px solid #fff;
      flex-grow: 1;
      margin: 3px;
      padding: 2px 2px 0 0;
   }

   &__header {
      align-items: center;
      border-bottom: 2px solid $yellow;
      cursor: default;
      display: flex;
      font-size: 1.125rem;
      height: 40px;
      position: relative;
   }

   &__title {
      margin: 0 0 0 10px;
   }

   &__progress-bar {
      background-color: #ffe69d;
      height: 40px;
      left: 0;
      position: absolute;
      top: 0;
      transition: .15s;
      width: 0;
      z-index: -1;
   }

   &__body {
      flex-grow: 1;
      max-height: 440px;
      overflow-x: hidden;
      overflow-y: auto;
   }

   &__actions {
      border-top: 2px solid $yellow;
   }

   & ::-webkit-scrollbar {
      width: 5px;
   }

   & ::-webkit-scrollbar-track,
   & ::-webkit-scrollbar-thumb {
      border-radius: 3px;
   }

}
</style>
