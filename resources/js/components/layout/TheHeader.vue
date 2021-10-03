<template>
<header class="header">
   <div class="header__nav">
      <router-link
         v-for="(section, index) in sections"
         :key="index"
         :to="section.route"
         v-ripple
         class="header__link p-ripple p-ripple--dark-green"
      >
         <FontAwesomeIcon class="header__icon" :icon="section.icon"/>
         <span class="header__link-label">{{ section.label }}</span>
      </router-link>
   </div>
   <div class="header__menu">
      <div @click="openMenu" class="header__link header__link--menu p-ripple p-ripple--dark-green">
         <FontAwesomeIcon class="header__icon" icon="bars"/>
         <span class="header__link-label">Меню</span>
      </div>
      <Menu ref="menu" :model="sections" :popup="true" class="header__options">
         <template #item="{ item }">
            <router-link
               @click="closeMenu"
               :to="item.route"
               v-ripple
               class="header__link p-ripple"
            >
               <FontAwesomeIcon class="header__icon" :icon="item.icon"/>
               <span class="header__link-label">{{ item.label }}</span>
            </router-link>
         </template>
      </Menu>
   </div>
   <div class="header__user">{{ userName }}</div>
   <div @click="logout" class="header__logout">
      <FontAwesomeIcon class="header__icon" icon="sign-out-alt"/>
   </div>
</header>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import store from '@/store';
import { Routes } from '@/router';
import { getSignSessionNavigationRoute } from '@/router/sign-sessions';
import { getConferenceNavigationRoute } from '@/router/conferences';
import Menu from 'primevue/menu';
import { Confirm } from '@/modules/modals/Confirm';

const menu = ref();
const openMenu = (event) => menu.value.toggle(event);
const closeMenu = () => menu.value.hide();

const sections = ref([
   {
      route: getSignSessionNavigationRoute(),
      icon: 'pen-alt',
      label: 'Сессии подписания'
   },
   {
      route: { name: Routes.PollsAtWork },
      icon: 'tasks',
      label: 'Опросы'
   },
   {
      route: { name: Routes.Vacations },
      icon: 'umbrella-beach',
      label: 'Отпуска'
   },
   {
      route: getConferenceNavigationRoute(),
      icon: 'users',
      label: 'Совещания'
   },
   {
      route: { name: Routes.Birthdays },
      icon: 'gift',
      label: 'Дни рождения'
   }
]);

const userName = computed(() => store.getters['user/getUserFio']);

const logout = () => {
   Confirm.logout(() => store.dispatch('auth/logout'));
};
</script>

<style lang="scss">
.header__options.p-menu {
   padding: unset;
   overflow: hidden;
}

</style>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.header {

   $h: &;
   background-color: $dark-green;

   color: #fff;
   box-shadow: 2px 0 5px rgb(0 0 0 / 22%);

   display: grid;
   grid-template-columns: 12fr 18fr 1fr;

   text-align: center;

   @extend %mb;


   padding: 0 0 0 20px;

   word-break: normal;

   @media screen and (max-width: $lg) {
      grid-template-columns: 12fr 9fr 1fr;
   }

   @media screen and (max-width: $md) {
      padding: 0 0 0 10px;
      display: flex;

      .header {
         &__nav {
            display: none;
         }

         &__menu {
            display: flex;
         }

         &__user {
            margin: 0 5px;
            flex-grow: 1;
         }
      }

   }

   &__icon {
      font-size: 1.125rem;
   }

   &__link-label {
      font-size: 0.875rem;
   }

   &__nav {
      display: grid;
      grid-template-columns: repeat(5, minmax(60px, 1fr));
   }

   &__link {

      color: #fff;
      cursor: pointer;
      transition: .15s;
      padding: 10px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;

      &:hover {
         background-color: #fff;
         color: $dark-green;
      }

      #{$h}__icon {
         margin: 0 0 5px 0;
      }

      &--menu {
         max-width: 150px;
      }
   }

   &__menu {
      display: none;
   }

   &__options {
      .header {

         &__link {
            color: $dark-green;

            &:hover {
               background-color: $dark-green;
               color: #fff;
            }
         }
      }
   }

   &__bars {
      font-size: 1.5rem;
      transition: background-color .15s, color .15s;

      &:hover {
         background-color: #fff;
         color: $dark-green;
      }
   }

   &__user {
      font-size: 1.125rem;
      cursor: default;
      text-align: end;
      align-self: center;
      margin: 0 20px 0 0;

      @media screen and (max-width: $md) {
         margin: 0 5px;
      }
   }

   &__logout {
      padding: 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
      color: #fff;
      transition: .15s;

      &:hover {
         background-color: #fff;
         color: $dark-green;
      }

      #{$h}__icon {
         font-size: 1.375rem;
      }
   }
}

</style>
