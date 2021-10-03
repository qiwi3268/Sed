<template>
<div @click="openFileSigner(file)" class="sign-state" :class="signState">
   <template v-if="signState === SignState.Checking">
      <FontAwesomeIcon class="sign-state__icon" icon="spinner"/>
   </template>
   <template v-else-if="signState === SignState.Valid">
      <FontAwesomeIcon class="sign-state__icon" icon="pen-alt"/>
   </template>
   <template v-else-if="signState === SignState.Warning">
      <FontAwesomeIcon class="sign-state__icon" icon="exclamation"/>
   </template>
   <template v-else>
      <FontAwesomeIcon class="sign-state__icon" icon="times"/>
   </template>
   <div class="sign-state__text">{{ label }}</div>
</div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { getSignStateLabel } from '@/modules/modals/files/utils';
import { GeFile } from '@/store/modules/modals/files/uploader';
import { openFileSigner } from '@/modules/modals/files/signer';
import { SignState } from '@/store/modules/modals/files/signer';

const props = defineProps<{
   file: GeFile
}>();

const signState = computed(() => props.file.signState);
const label = computed(() => getSignStateLabel(signState.value));
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.sign-state {
   align-items: center;
   border-radius: 3px;
   cursor: pointer;
   display: flex;
   font-size: 0.875rem;
   padding: 7px;
   transition: .15s;
   margin: 0 5px 0 0;

   &__icon {
      margin: 0 3px 0 0;
   }

   &__text {
      white-space: nowrap;
   }

   &.checking {
      background-color: $background-blue;
      border: 1px solid #cad6e7;
      color: #6a7e9a;

      &:hover {
         background-color: #d4e4fa;
      }
   }

   &.green {
      background-color: $background-green;
      border: 1px solid #c5e6c1;
      color: #537a5d;

      &:hover {
         background-color: #d9f0d6;
      }
   }

   &.orange {
      background-color: #fcf4d9;
      border: 1px solid #e4dab7;
      color: #9f8e53;

      &:hover {
         background-color: #f7ecc7;
      }
   }

   &.red,
   &.notSigned {
      background-color: $background-red;
      border: 1px solid #e8cbcb;
      color: #a57474;

      &:hover {
         background-color: #f9dada;
      }
   }

}

</style>
