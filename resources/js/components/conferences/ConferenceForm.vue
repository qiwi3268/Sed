<template>
<div class="row-form">
   <BaseInput label="Тема" :field="v$.topic"/>
   <DateField
      label="Дата начала"
      :field="v$.startAt"
      :showTime="true"
   />
   <BaseDropdown label="Форма" :field="v$.conferenceForm" :alias="ConferenceMiscs.Form"/>
   <BaseTextarea label="Ссылка на подключение" :field="v$.vksHref"/>
   <GeUser label="Ответственный за подключение" :field="v$.vksConnectionResponsible"/>
   <BaseDropdown label="Место проведения" :field="v$.conferenceLocation" :alias="ConferenceMiscs.Location"/>
   <GeUsers label="Участники" :field="v$.members"/>
   <BaseTextarea label="Внешние участники" :field="v$.outerMembers"/>
   <BaseTextarea label="Комментарий" :field="v$.comment"/>
</div>
</template>

<script setup lang="ts">
import { computed, reactive, watch } from 'vue';
import store from '@/store';
import useVuelidate from '@vuelidate/core';
import BaseInput from '@/components/forms/fields/BaseInput.vue';
import GeUsers from '@/components/forms/fields/GeUsers.vue';
import BaseDropdown from '@/components/forms/fields/BaseDropdown.vue';
import BaseTextarea from '@/components/forms/fields/BaseTextarea.vue';
import GeUser from '@/components/forms/fields/GeUser.vue';
import DateField from '@/components/forms/fields/DateField.vue';
import { ConferenceForm, ConferenceForms, ConferenceMiscs } from '@/types/conferences';

const props = defineProps<{
   model: ConferenceForm
   saving: boolean
}>();

const emit = defineEmits(['save']);


const form = reactive(props.model);

const isVks = computed(() => {
   return form.conferenceForm !== null &&
      [ConferenceForms.Vks, ConferenceForms.VksIntramural].includes(form.conferenceForm.id);
});

const rules = computed(() => store.getters['conferences/getConferenceRules']);
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
