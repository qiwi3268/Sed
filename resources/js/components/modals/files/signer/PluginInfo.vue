<template>
<div class="sign-modal__header">
   <div class="sign-modal__plugin-row">
      <span class="sign-modal__plugin-label">Версия плагина: </span>
      <span class="sign-modal__text">{{ plugin.version }}</span>
   </div>
   <div class="sign-modal__plugin-row">
      <span class="sign-modal__plugin-label">Версия криптопровайдера: </span>
      <span class="sign-modal__text">{{ plugin.cspVersion }}</span>
   </div>
</div>
</template>

<script setup lang="ts">
import { CspPlugin } from '@/store/modules/modals/files/signer';

const props = defineProps<{
   plugin: CspPlugin
}>();
</script>

<style scoped lang="scss">
</style>
