<template>
<div class="card-overlay p-ripple p-ripple--dark-green" v-ripple>
   <div class="card-overlay__background"></div>
   <div class="card-overlay__body">
      <div class="card-overlay__button">
         <div class="card-overlay__text">{{ label }}</div>
      </div>
   </div>
</div>
</template>

<script setup lang="ts">
const props = defineProps<{
   label: string
}>();

</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.card-overlay {
   position: absolute;

   width: 100%;
   height: 100%;

   color: #fff;

   border-radius: 4px;
   transition: border-color .15s;

   &:hover {
      .card-overlay__background {
         opacity: 0.45;
      }

      .card-overlay__body {
         opacity: 1;
      }
   }

   &__background {
      position: absolute;

      background-color: $dark-green;

      width: 100%;
      height: 100%;

      opacity: 0;
      transition: opacity .15s;
   }

   &__body {
      position: absolute;

      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 1.875rem;
      width: 100%;
      height: 100%;
      transition: opacity .15s;
      opacity: 0;

      @media screen and (max-width: $md) {
         font-size: 1.5rem;
      }

      @media screen and (max-width: $sm) {
         font-size: 1.25rem;
      }
   }

   &__button {
      display: flex;
      align-items: center;

      font-weight: 300;
      font-style: italic;
   }
}

</style>
