<template>
<div v-if="file" class="files__item">
   <FileInfo :file="file"/>
   <div v-if="showSign" @click="openSignViewer(file)" class="sign-state" :class="signState">
      <FontAwesomeIcon class="sign-state__icon" :icon="icon"/>
      <div class="sign-state__text">{{ label }}</div>
   </div>
   <div class="files__actions">
      <Button
         @click="download"
         v-tooltip="'Скачать файл'"
         class="files__download p-button-outlined"
      >
         <i class="pi pi-download"></i>
      </Button>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { GeFile } from '@/store/modules/modals/files/uploader';
import { getSignStateIcon, getSignStateLabel } from '@/modules/modals/files/utils';
import { openSignViewer } from '@/modules/modals/files/sign-viewer';
import FileInfo from '@/components/forms/elements/FileInfo.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import store from '@/store';
import Button from 'primevue/button';

const props = defineProps<{
   file: GeFile
   showSign?: boolean
}>();

const signState = computed(() => props.file.signState);
const icon = computed(() => getSignStateIcon(signState.value));
const label = computed(() => getSignStateLabel(signState.value));

const download = () => {
   store.dispatch('files/downloadFile', props.file.starPath);
};
</script>


<style scoped lang="scss">
@use 'resources/scss/forms/files';
</style>
