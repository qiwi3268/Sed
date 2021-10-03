<template>
<div class="form-field"
     :class="{ invalid: unit.$error, required: unit.mRequired || unit.required }"
>
   <div class="form-field__label">{{ label }}</div>
   <div class="form-field__body">
      <Calendar
         v-model="unit.$model"
         placeholder="Выберите дату"
         :minDate="minDate"
         :maxDate="maxDate"
         :selectOtherMonths="true"
         :manualInput="false"
         :showTime="showTime"
         :stepMinute="5"
      />
      <div v-if="unit.$error" class="form-field__error">{{ unit.$errors[0].$message }}</div>
   </div>
</div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import Calendar from 'primevue/calendar';
import { DateInterval, FormUnit } from '@/modules/forms/validators';

const props = defineProps<{
   label: string
   field: FormUnit<Date | null>
   interval?: DateInterval
   showTime?: boolean
}>();

const unit = reactive(props.field);
const minDate = props.interval === DateInterval.Future ? new Date() : new Date(1700, 0, 1);
const maxDate = props.interval === DateInterval.Past ? new Date() : new Date(2100, 0, 1);
</script>


<style scoped lang="scss">
</style>
