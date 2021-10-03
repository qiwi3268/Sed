<template>
<div class="medium-wrapper">
   <div class="sub-header">
      <div class="sub-header__title">Редактирование совещания</div>
      <SubHeaderActions @ok="saving = true" @cancel="returnToView" :handlingAction="saving"/>
   </div>
   <ConferenceForm v-if="model" :model="model" :saving="saving" @save="save"/>
</div>
</template>

<script setup lang="ts">
import store from '@/store';
import { useRouteLeaveGuard } from '@/composables/navigation';
import { computed, ref } from 'vue';
import ConferenceForm from '@/components/conferences/ConferenceForm.vue';
import SubHeaderActions from '@/components/widgets/SubHeaderActions.vue';
import { isFuture } from 'date-fns';
import { Toast } from '@/modules/notification/Toast';

const model = computed(() => store.state.conferences.conferenceEdit);

const saving = ref(false);
const save = async(v$) => {
   const isValid = await v$.$validate();
   if (isValid && saving.value && model.value) {
      if (!isFuture(model.value.startAt!)) {
         Toast.invalidConferenceCreationDate();
         model.value.startAt = null;
      } else {
         await store.dispatch('conferences/updateConference', model.value);
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
