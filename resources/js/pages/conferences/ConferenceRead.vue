<template>
<div class="small-wrapper">
   <div class="sub-header">
      <div class="sub-header__title">Просмотр совещания</div>
      <div class="sub-header__actions">
         <ActionButton
            v-if="canDelete || deleting"
            @click="remove"
            label="Удалить"
            :loading="deleting"
            modifier="red"
         />
         <ActionButton
            v-if="canUpdate || routing"
            @click="edit"
            label="Редактировать"
            :loading="routing"
            modifier="green"
         />
         <ActionButton @click="returnToView" label="Выйти"/>
      </div>
   </div>
   <ConferenceFormRead :model="model"/>
</div>
</template>

<script setup lang="ts">
import ConferenceFormRead from '@/components/conferences/ConferenceFormRead.vue';
import { computed, ref } from 'vue';
import store from '@/store';
import { goToEditConference, returnToConferenceNavigation } from '@/router/conferences';
import ActionButton from '@/components/widgets/ActionButton.vue';
import { Confirm } from '@/modules/modals/Confirm';
import { ConferenceRead } from '@/types/conferences';

const model = computed<ConferenceRead | null>(() => store.state.conferences.conferenceRead);

const canDelete = computed(() => store.state.conferences.canDeleteConference);
const deleting = ref(false);
const remove = async() => {
   if (model.value && !routing.value && !deleting.value) {
      Confirm.deleteConference(async() => {
         deleting.value = true;
         if (model.value) await store.dispatch('conferences/deleteConference', model.value.uuid);
         deleting.value = false;
      });
   }
};

const canUpdate = computed(() => store.state.conferences.canUpdateConference);
const routing = ref(false);
const edit = async() => {
   if (model.value && !routing.value && !deleting.value) {
      routing.value = true;
      await goToEditConference(model.value.uuid);
      routing.value = false;
   }
};

const returnToView = () => returnToConferenceNavigation();
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/layout/sub-header';
@use 'resources/scss/elements/action-button';

.sub-header {

   &__actions {
      align-self: stretch;
      display: flex;
      @extend %gcg;

      @media screen and (max-width: $md) {
         grid-template-columns: repeat(3, minmax(60px, 1fr));
      }

      @media screen and (max-width: $sm) {
         grid-template-columns: 1fr 1fr;
      }
   }
}
</style>
