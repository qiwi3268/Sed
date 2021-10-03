<template>
<div class="session-read">
   <div class="session-read__block">
      <div class="session-read__row row-form">
         <FileFieldRead :files="[model.file]" :showSign="false">
            <template #label>
               <div  class="form-field__action">
                  <div class="form-field__action-label">Файл</div>
                  <Button
                     v-if="!signed"
                     @click="$emit('addSign')"
                     label="Подписать"
                     class="p-button-outlined"
                  />
                  <Button
                     v-else
                     @click="$emit('removeSign')"
                     label="Удалить подпись"
                     class="p-button-outlined p-button-danger"
                  />
               </div>
            </template>
         </FileFieldRead>
      </div>
   </div>
</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import FileFieldRead from '@/components/forms/fields/read/FileFieldRead.vue';
import Button from 'primevue/button';
import { SignSessionDetails } from '@/types/sign-sessions';

const props = defineProps<{
   model: SignSessionDetails
}>();

const emit = defineEmits(['addSign', 'removeSign']);

const signed = computed(() => props.model.file.validationResult.length > 0);
</script>

<style lang="scss">
@use 'resources/scss/forms/row-form';
@use 'resources/scss/sign-sessions/session-read';
</style>
