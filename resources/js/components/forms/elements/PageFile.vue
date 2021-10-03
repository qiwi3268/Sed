<template>
<div class="files__item">
   <FileInfo :file="file"/>
   <FileSignState v-if="canSign" :file="file"/>
   <div class="files__actions">
      <Button
         @click="download"
         v-tooltip="'Скачать файл'"
         class="files__download p-button-outlined"
      >
         <i class="pi pi-download"></i>
      </Button>
      <Button
         @click="remove"
         v-tooltip="'Удалить файл'"
         class="p-button-outlined p-button-danger files__remove"
      >
         <i class="pi pi-minus"></i>
      </Button>
   </div>
</div>
</template>

<script setup lang="ts">
import { GeFile } from '@/store/modules/modals/files/uploader';
import FileInfo from '@/components/forms/elements/FileInfo.vue';
import FileSignState from '@/components/forms/elements/FileSignState.vue';
import store from '@/store';
import Button from 'primevue/button';

const props = defineProps<{
   file: GeFile
   canSign: boolean
}>();

const emit = defineEmits(['remove']);

const remove = () => emit('remove', props.file);
const download = () => {
   store.dispatch('files/downloadFile', props.file.starPath);
};
</script>

<style scoped lang="scss">
@use 'resources/scss/forms/files';

.files {
   &__item {
      &:not(&:last-child) {
         border-bottom: 1px solid #818e9a;
      }
   }
}

</style>
