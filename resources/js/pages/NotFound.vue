<template>
<div class="not-found">
   <div class="not-found__title">Страница не найдена</div>
   <router-link :to="{ name: Routes.Home }" class="not-found__link">Вернуться на главную</router-link>
</div>
</template>

<script setup lang="ts">
import { Routes } from '@/router';
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.not-found {
   background-color: $dark-green;
   flex-grow: 1;
   display: flex;
   align-items: center;
   justify-content: center;
   flex-direction: column;

   &__title {
      font-size: 2.5rem;
      color: #fff;
      margin: 0 0 50px 0;
   }

   &__link {
      font-size: 1.5rem;
      color: #fff;
   }
}
</style>
