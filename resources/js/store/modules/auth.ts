import { ActionContext } from 'vuex';
import store, { RootState } from '@/store';
import router, { forceChangeRoute, Routes } from '@/router';
import auth, { LOGIN_URL, LOGOUT_URL } from '@/plugins/auth';
import { getSignSessionNavigationRoute } from '@/router/sign-sessions';
import { Toast } from '@/modules/notification/Toast';
import { AxiosError } from 'axios';

export type LoginForm = {
   email: string
   password: string
}

export type AuthState = {
}

export default {
   namespaced: true,
   state: (): AuthState => ({}),
   mutations: {},
   actions: {
      async login(context: ActionContext<AuthState, RootState>, form: LoginForm): Promise<void> {
         // Запоминаем путь, если пришли по ссылке, но выкинуло на логин
         const redirect = auth.redirect();

         try {
            await auth.login({
               url: LOGIN_URL,
               data: form,
               fetchUser: false,
               staySignedIn: false
            });
            await store.dispatch('user/fetchUser');
            router.push(redirect ? redirect.from : getSignSessionNavigationRoute());
         } catch (exc) {
            const errorMessage = (exc as AxiosError).response?.status === 401
               ? 'Неверный логин или пароль'
               : 'Неизвестная ошибка, обратитель к администратору';

            Toast.loginError(errorMessage);
         }
      },
      logout(): void {
         store.commit('user/setUser', null);
         auth.logout({ url: LOGOUT_URL, makeRequest: true });
         forceChangeRoute({ name: Routes.Login });
      }
   },
   getters: {}
};


