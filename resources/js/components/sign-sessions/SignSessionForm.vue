<template>
<div class="sign-session-form">
   <div class="row-form">
      <BaseInput label="Заголовок" :field="v$.title"/>
      <FileField label="Файл" :field="v$.m1m1m1" mapping="m1m1m1"/>
      <GeUsers label="Подписанты" :field="v$.signerIds"/>
   </div>
</div>
<transition name="fade">
   <div v-if="isFileUploaderOpened">
      <FileUploader/>
   </div>
</transition>
</template>

<script setup lang="ts">
import { computed, reactive, watch } from 'vue';
import useVuelidate from '@vuelidate/core';
import FileField from '@/components/forms/fields/FileField.vue';
import { isFileUploaderOpened } from '@/modules/modals/files/uploader';
import FileUploader from '@/components/modals/files/FileUploader.vue';
import GeUsers from '@/components/forms/fields/GeUsers.vue';
import BaseInput from '@/components/forms/fields/BaseInput.vue';
import store from '@/store';
import { SignSession } from '@/types/sign-sessions';

const props = defineProps<{
   model: SignSession
   saving: boolean
}>();

const emit = defineEmits(['save']);

const form = reactive<SignSession>(props.model);

const rules = computed(() => store.getters['signSessions/getSignSessionRules']);
const v$ = useVuelidate(rules, form);

watch(
   () => props.saving,
   () => {
      if (props.saving) {
         v$.value.$reset();
         v$.value.$touch();
         emit('save', v$.value);
      }
   },
   { immediate: false }
);

</script>

<style lang="scss">
@use 'resources/scss/elements/form-field';
@use 'resources/scss/forms/row-form';
</style>
