<template>
<div v-if="model" class="conference-read form-read row-form">
   <InputRead label="Тема" :value="model.topic"/>
   <DateFieldRead label="Дата начала" :value="model.startAt" :showTime="true"/>
   <InputRead label="Форма" :value="model.conferenceForm"/>
   <LinkRead label="Ссылка на подключение" :value="model.vksHref"/>
   <InputRead label="Ответственный за подключение" :value="model.connectionResponsible"/>
   <InputRead label="Место проведения" :value="model.conferenceLocation"/>
   <MultiselectRead label="Участники" :value="model.members"/>
   <InputRead label="Внешние участники" :value="model.outerMembers"/>
   <InputRead label="Комментарий" :value="model.comment"/>
</div>
</template>


<script setup lang="ts">
import InputRead from '@/components/forms/fields/read/InputRead.vue';
import LinkRead from '@/components/forms/fields/read/LinkRead.vue';
import MultiselectRead from '@/components/forms/fields/read/MultiselectRead.vue';
import DateFieldRead from '@/components/forms/fields/read/DateFieldRead.vue';
import { ConferenceRead } from '@/types/conferences';

const props = defineProps<{
   model: ConferenceRead
}>();

</script>

<style lang="scss">
@use 'resources/scss/forms/row-form';
@use 'resources/scss/elements/form-field';

.form-read {
   .form-field {
      cursor: default;
      display: grid;
      grid-template-columns: 3fr 7fr;
      grid-column-gap: 10px;
      align-items: center;

      &__label {
         text-align: right;
         margin: unset;
      }
   }
}
</style>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.conference-read {
   @extend %card;
}
</style>
