<template>
<button
   v-if="true"
   @click="click"
   class="action-button p-ripple"
   :class="[modifier]"
   v-ripple
>
   <ProgressSpinner v-if="loading" animationDuration=".8s" class="action-button__spinner"/>
   <div v-else class="action-button__label">{{ label }}</div>
</button>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import ProgressSpinner from 'primevue/progressspinner';

const props = defineProps<{
   label: string
   loading?: boolean
   disabled?: boolean
   modifier?: string
}>();

const emit = defineEmits(['click']);

const click = () => {
   if (!props.disabled && !props.loading) {
      emit('click');
   }
};

const modifier = computed(() => props.modifier ? `action-button--${props.modifier}` : '');
</script>

<style scoped lang="scss">
@use 'resources/scss/elements/action-button';
</style>
