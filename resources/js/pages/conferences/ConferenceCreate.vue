<template>
<div class="medium-wrapper">
   <div class="sub-header">
      <div class="sub-header__title">Создание совещания</div>
      <SubHeaderActions @ok="saving = true" @cancel="returnToView" :handlingAction="saving"/>
   </div>
   <ConferenceForm :model="model" :saving="saving" @save="save"/>
</div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue';
import SubHeaderActions from '@/components/widgets/SubHeaderActions.vue';
import ConferenceForm from '@/components/conferences/ConferenceForm.vue';
import store from '@/store';
import { isFuture } from 'date-fns';
import { useRouteLeaveGuard } from '@/composables/navigation';
import { Toast } from '@/modules/notification/Toast';

const model = reactive(store.getters['conferences/getEmptyConfidenceForm']);

const saving = ref(false);
const save = async(v$) => {
   const isValid = await v$.$validate();
   if (isValid && saving.value) {
      if (!isFuture(model.startAt)) {
         Toast.invalidConferenceCreationDate();
         model.startAt = null;
      } else {
         await store.dispatch('conferences/saveConference', model);
      }
   }
   saving.value = false;
};

const returnToView = () => store.dispatch('conferences/returnToSelectedView');
useRouteLeaveGuard();
</script>

<style scoped lang="scss">
@use 'resources/scss/layout/sub-header';
@use 'resources/scss/elements/action-button';
</style>
