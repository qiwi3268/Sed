<template>
<div v-if="hasFiles" class="form-field">
   <div class="form-field__label">
      <slot name="label">{{ label }}</slot>
   </div>
   <div class="file-field__files files">
      <template v-for="file in files" :key="file.starPath">
         <PageFileRead :file="file" :showSign="showSign"/>
      </template>
   </div>
</div>
</template>

<script setup lang="ts">
import { GeFile } from '@/store/modules/modals/files/uploader';
import PageFileRead from '@/components/forms/fields/read/PageFileRead.vue';

const props = defineProps<{
   label?: string
   files: GeFile[]
   showSign?: boolean
}>();

const hasFiles = props.files.length > 0;
</script>

<style scoped lang="scss">

.file-field {
   &__files {
      border-radius: 4px;
      border: 1px solid #818e9a;
   }
}

</style>
