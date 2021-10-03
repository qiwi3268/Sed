<template>
<div @click="$emit('click')" class="refresh-button p-ripple" v-ripple>Обновить информацию</div>
</template>

<script setup lang="ts">
const emit = defineEmits(['click']);
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.refresh-button {
   cursor: pointer;
   color: #fff;
   background-color: $green-blue;
   border-radius: 4px;
   text-align: center;
   transition: background-color .15s;
   padding: 10px 15px;

   @media screen and (max-width: $md) {
      padding: 7px 10px;
   }

   &:hover {
      background-color: $green-blue-hov;
   }
}
</style>
