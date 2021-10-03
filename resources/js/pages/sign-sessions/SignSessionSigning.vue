<template>
<div class="medium-wrapper">
   <div class="session-signing">
      <div class="session-signing__cards">
         <div class="sub-header">
            <div class="sub-header__title">Подписание</div>
            <SubHeaderActions @ok="save" @cancel="returnToView" :handlingAction="saving"/>
         </div>
         <div class="session-signing__block">
            <SignSessionFormSigning :model="model" @addSign="addSign" @removeSign="removeSign"/>
         </div>
         <template v-if="signed">
            <div v-for="(sign, index) in model.file.validationResult" :key="index" class="session-signing__card">
               <ValidationResult :validationResult="sign"/>
            </div>
         </template>
      </div>
   </div>
</div>
<transition name="fade">
   <div v-if="isFileSignerOpened">
      <FileSigner/>
   </div>
</transition>
</template>

<script setup lang="ts">
import { computed, reactive, ref } from 'vue';
import SubHeaderActions from '@/components/widgets/SubHeaderActions.vue';
import SignSessionFormSigning from '@/components/sign-sessions/SignSessionFormSigning.vue';
import store from '@/store';
import { openFileSigner } from '@/modules/modals/files/signer';
import FileSigner from '@/components/modals/files/signer/FileSigner.vue';
import ValidationResult from '@/components/modals/files/signer/ValidationResult.vue';
import { useRouteLeaveGuard } from '@/composables/navigation';
import { Toast } from '@/modules/notification/Toast';
import { SignSessionDetails } from '@/types/sign-sessions';

const model = reactive<SignSessionDetails>(store.state.signSessions.signSessionSigning!);
const signing = ref(false);

const signed = computed(() => model.file.validationResult.length > 0);
const saving = ref(false);
const save = async() => {
   if (!signed.value || !model.file.validationResult[0].signatureResult) {
      Toast.invalidSignSessionSigning();
   } else if (model.file.validationResult.length > 1) {
      Toast.multipleSigns();
   } else if (!saving.value) {
      saving.value = true;
      await store.dispatch('signSessions/saveSignSessionSigning', model);
      saving.value = false;
   }
};

const returnToView = () => store.dispatch('signSessions/returnToSelectedView');
useRouteLeaveGuard();

const addSign = () => {
   openFileSigner(model.file);
};
const removeSign = () => {
   model.file.validationResult = [];
};

const isFileSignerOpened = computed(() => store.state.fileSigner.opened);
</script>

<style lang="scss">
@use 'resources/scss/elements/action-button';
@use 'resources/scss/layout/sub-header';

.session-signing {
   align-items: flex-start;
   display: flex;

   &__block {
      margin: 0 0 10px 0;
   }

   &__cards {
      flex-grow: 1;
   }

   &__card {
      @extend %card;

      &:not(&:last-child) {
         margin: 0 0 10px 0;
      }
   }
}

</style>
