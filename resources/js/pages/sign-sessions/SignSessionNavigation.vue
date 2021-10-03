<template>
<div class="sign-sessions-navigation">
   <div class="navigation-sidebar">
         <router-link :to="{ name: Routes.SignSessionCreate }" class="navigation-sidebar__actions">
            <div class="navigation-sidebar__create p-ripple" v-ripple>Создать сессию подписания</div>
         </router-link>
      <div class="navigation-sidebar__block">
         <router-link :to="getSignSessionNavigationRoute()">
            <div class="navigation-sidebar__title p-ripple p-ripple--dark-green" v-ripple>Сессии по статусу</div>
         </router-link>
         <NavigationLabel
            label="Ожидают действия"
            :viewName="SignSessionViews.WaitingAction"
            :hasPagination="true"
         />
         <NavigationLabel
            label="Выполняется"
            :viewName="SignSessionViews.InWork"
            :hasPagination="true"
         />
         <NavigationLabel
            label="Выполнено"
            :viewName="SignSessionViews.Finished"
            :hasPagination="true"
         />
      </div>
   </div>
   <div class="list-view">
      <router-view/>
   </div>
</div>
</template>

<script setup lang="ts">
import { Routes } from '@/router';
import { getSignSessionNavigationRoute } from '@/router/sign-sessions';
import { SignSessionViews } from '@/types/sign-sessions';
import NavigationLabel from '@/components/widgets/NavigationLabel.vue';
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/layout/navigation-sidebar';
@use 'resources/scss/elements/action-button';

.sign-sessions-navigation {
   @extend %navigation;

   .list-view {
      flex-grow: 1;
   }
}

</style>
