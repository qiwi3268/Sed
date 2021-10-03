<template>
<div class="form-field"
     :class="{ invalid: unit.$error, required: unit.mRequired || unit.required }"
>
   <div class="form-field__label">{{ label }}</div>
   <div class="form-field__body file-field" :class="{ filled: hasFiles }">
      <div v-if="canAddFiles" @click="openFileUploader(mapping, files)" class="file-field__select form-field__select">
         <div class="file-field__title">{{ !hasFiles ? 'Загрузить' : 'Добавить файлы' }}</div>
         <FontAwesomeIcon icon="plus"/>
      </div>
      <div v-if="hasFiles" class="file-field__files files">
         <template v-for="file in files" :key="file.starPath">
            <PageFile :file="file" :canSign="canSign" @remove="removeFile"></PageFile>
         </template>
      </div>
      <div v-if="unit.$error" class="form-field__error">{{ unit.$errors[0].$message }}</div>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed, reactive } from 'vue';
import { GeFile } from '@/store/modules/modals/files/uploader';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import PageFile from '@/components/forms/elements/PageFile.vue';
import { openFileUploader } from '@/modules/modals/files/uploader';
import store from '@/store';
import { FormUnit } from '@/modules/forms/validators';

const props = defineProps<{
   label: string
   field: FormUnit<GeFile[]>
   mapping: string
   canSign?: boolean
}>();

const unit = reactive(props.field);
const files = computed(() => unit.$model);

store.dispatch('filesUploader/fetchFieldRules', props.mapping);

const hasFiles = computed(() => files.value.length > 0);

const removeFile = (geFile: GeFile) => {
   files.value.splice(files.value.indexOf(geFile), 1);
};

const multiple = computed(() => store.getters['filesUploader/isMultiple'](props.mapping));
const canAddFiles = computed(() => multiple.value || files.value.length === 0);
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.file-field {
   $f: &;

   &__files {
      border-radius: 4px;
      border: 1px solid #818e9a;

      &:not(&:first-child) {
         border-radius: 0 0 4px 4px;
         border-top: 0;
      }
   }

   &__select {
      @extend %field;
      padding: 8px;
      justify-content: space-between;

      @media screen and (max-width: $md) {
         padding: 7px;
      }

      @media screen and (max-width: $sm) {
         padding: 6px;

      }
   }

   &.filled {

      #{$f}__select {
         border-color: #818e9a;
         border-radius: 4px 4px 0 0;
         color: #1c3959;
         background-color: #f8f9fa;

         &:hover {
            border-color: $active-blue;
            box-shadow: 0 0 0 3px $shadow-blue;
         }
      }

   }
}

</style>
