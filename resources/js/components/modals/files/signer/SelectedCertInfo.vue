<template>
<div class="sign-modal__cert-info" :class="{ invalid: !certInfo.certStatus }">
   <div class="sign-modal__cert-row">
      <span class="sign-modal__label">Данные о выбранном сертификате:</span>
   </div>
   <div class="sign-modal__cert-row">
      <span class="sign-modal__label">Владелец:</span>
      <span class="sign-modal__text">{{ certInfo.subjectName }}</span>
   </div>
   <div class="sign-modal__cert-row">
      <span class="sign-modal__label">Издатель:</span>
      <span class="sign-modal__text">{{ certInfo.issuerName }}</span>
   </div>
   <div class="sign-modal__cert-row">
      <span class="sign-modal__label">Дата выдачи:</span>
      <span class="sign-modal__text">{{ certInfo.validFromDate }}</span>
   </div>
   <div class="sign-modal__cert-row">
      <span class="sign-modal__label">Срок действия:</span>
      <span class="sign-modal__text">{{ certInfo.validToDate }}</span>
   </div>
   <div class="sign-modal__cert-row">
      <span class="sign-modal__label">Статус:</span>
      <span class="sign-modal__status">{{ certInfo.certMessage }}</span>
   </div>
</div>
</template>

<script setup lang="ts">
import { CertInfo } from '@/store/modules/modals/files/signer';

const props = defineProps<{
   certInfo: CertInfo
}>();
</script>

<style scoped lang="scss">
.sign-modal {

   &__label {
      font-weight: 700;
      margin: 0 5px 0 0;
   }
}
</style>
