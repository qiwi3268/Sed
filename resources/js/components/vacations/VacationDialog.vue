<template>
<div class="vacation-dialog">
   <Dialog
      header="Добавление отпуска"
      v-model:visible="visible"
      @show="reset"
      :modal="true"
      :closable="false"
      class="vacation-dialog"
      @hide="$emit('hide')"
   >
      <div class="row-form vacation-dialog__form">
         <GeUser label="Сотрудник" :field="v$.employee"/>
         <DateField label="Дата начала" :field="v$.startAt"/>
         <BaseInputNumber label="Длительность" :field="v$.duration"/>
         <GeUsers label="Замещение" :exclude="selectedEmployee" :field="v$.replacementEmployees"/>
      </div>

      <template #footer>
         <Button label="Ок" icon="pi pi-check" @click="validate"/>
         <Button label="Отмена" icon="pi pi-times" @click="cancel" class="p-button-text" />
      </template>

   </Dialog>
</div>
</template>

<script lang="ts">
import { computed, defineComponent, PropType, reactive } from 'vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import GeUsers from '@/components/forms/fields/GeUsers.vue';
import { FormUnit } from '@/modules/forms/validators';
import BaseInputNumber from '@/components/forms/fields/BaseInputNumber.vue';
import DateField from '@/components/forms/fields/DateField.vue';
import GeUser from '@/components/forms/fields/GeUser.vue';

export default defineComponent({
   name: 'VacationDialog',
   components: {
      BaseInputNumber,
      Dialog,
      Button,
      GeUser,
      GeUsers,
      DateField
   },
   props: {
      visible: {
         type: Boolean
      },
      form: {
         type: Object as PropType<FormUnit>,
         required: true
      }
   },
   emits: {
      'update:visible': null,
      save: null,
      cancel: null,
      hide: null

   },
   setup(props, { emit }) {
      const v$ = reactive<any>(props.form);

      const selectedEmployee = computed(() => v$.employee.$model ? [v$.employee.$model] : []);

      const reset = () => v$.$reset();

      const validate = () => {
         v$.$touch();
         v$.$validate()
            .then(isValid => {
               if (isValid) {
                  emit('save', v$.key.$model);
                  close();
               }
            });
      };

      const cancel = () => {
         emit('cancel');
         close();
      };

      const close = () => emit('update:visible', false);

      return {
         v$,
         reset,
         selectedEmployee,
         validate,
         cancel,
         close
      };
   }
});
</script>

<style lang="scss">
@use 'resources/scss/abstract' as *;
@use 'resources/scss/forms/row-form';
@use 'resources/scss/elements/form-field';

.vacation-dialog {
   width: 800px;

   @media screen and (max-width: $lg) {
      width: 700px;
   }

   @media screen and (max-width: $md) {
      width: 400px;
   }

   @media screen and (max-width: $sm) {
      width: unset;
   }

   .p-dialog-header {
      padding: 15px 25px;
      border-bottom: 1px solid $border;
   }

   .p-dialog-content {
      padding: 15px 25px;
      border-bottom: 1px solid $border;
   }

   .p-dialog-footer {
      padding: 15px 25px;
   }

  /* .p-button.p-component {
      position: unset;
   }*/

   &__form.row-form {
      border-radius: unset;
      box-shadow: unset;
      padding: 3px;
   }

   .misc-modal {
      top: 0;
   }

   .form-field {
      &:not(&:last-child) {
         padding: 0 0 25px 0;
         margin: 0 0 25px 0;

         @media screen and (max-width: $md) {
            padding: 0 0 15px 0;
            margin: 0 0 15px 0;
         }

      }

   }
}


</style>


