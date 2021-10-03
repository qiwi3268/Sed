<template>
<div class="medium-wrapper">
   <div class="sub-header">
      <div class="sub-header__title">Создание сессии подписания</div>
      <SubHeaderActions @ok="saving = true" @cancel="returnToView" :handlingAction="saving"/>
   </div>
   <SignSessionForm :model="model" :saving="saving" @save="save"/>
</div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import SignSessionForm from '@/components/sign-sessions/SignSessionForm.vue';
import store from '@/store';
import SubHeaderActions from '@/components/widgets/SubHeaderActions.vue';
import { useRouteLeaveGuard } from '@/composables/navigation';

const model = store.getters['signSessions/getSignSessionEmptyForm'];

const saving = ref(false);
const save = async(v$) => {
   const isValid = await v$.$validate();
   if (isValid && saving.value) {
      await store.dispatch('signSessions/saveSignSession', model);
   }
   saving.value = false;
};

const returnToView = () => store.dispatch('signSessions/returnToSelectedView');
useRouteLeaveGuard();
</script>

<style scoped lang="scss">
@use 'resources/scss/layout/sub-header';
@use 'resources/scss/elements/action-button';

</style>
