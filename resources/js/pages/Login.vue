<template>
<div class="form-login">
   <div class="form-login__title">Вход в CЭД</div>
   <div class="form-login__body p-fluid">
      <InputText v-model="form.email" class="form-login__field" placeholder="Логин" type="login"/>
      <Password
         v-model="form.password"
         :feedback="false"
         toggleMask
         placeholder="Пароль"
         class="form-login__field"
      />
      <Button @click="login" label="Войти" class="form-login__button"/>
   </div>
</div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import Password from 'primevue/password';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import store from '@/store';
import { LoginForm } from '@/store/modules/auth';
import { Toast } from '@/modules/notification/Toast';

const form = reactive<LoginForm>({
   email: '',
   password: ''
});

const login = () => {
   if (!form.email || !form.password) {
      Toast.loginAndPasswordRequired();
   } else {
      store.dispatch('auth/login', form);
   }
};
</script>

<style scoped lang="scss">
@use 'resources/scss/abstract' as *;

.form-login {
   text-align: center;
   align-self: center;
   width: 300px;
   margin: 100px 0 0 0;
   border-radius: 4px;
   background-color: #fff;
   box-shadow: 1px 0 3px rgba(0, 0, 0, 0.15);
   padding: 20px;

   &__title {
      margin: 0 0 20px;
      font-size: 1.375rem;
      user-select: none;
   }

   &__button {
      width: 100px;
   }

   &__field {
      margin: 0 0 10px 0;
      cursor: default;

   }

   &__remember {
      margin: 0 5px 0 0;
   }

}

</style>
