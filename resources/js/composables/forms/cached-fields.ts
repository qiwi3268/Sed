import { onMounted, onUnmounted } from 'vue';
import { FormUnit } from '@/modules/forms/validators';

/**
 * Добавляет в компонент кэширование поля из формы объекта vuelidate
 *
 * @param field - кэшируемое поле
 */
export const useCachedField = (field: FormUnit): void => {
   onMounted(() => {
      if (field.modelBeforeUnmount) field.$model = field.modelBeforeUnmount;
   });

   onUnmounted(() => {
      field.modelBeforeUnmount = field.$model;
      field.$model = Array.isArray(field.$model) ? [] : null;
   });
};
