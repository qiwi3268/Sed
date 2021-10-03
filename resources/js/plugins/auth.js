import { createAuth } from '@websanova/vue-auth/src/v3.js';
import driverHttpAxios from '@websanova/vue-auth/src/drivers/http/axios.1.x.js';
import driverRouterVueRouter from '@websanova/vue-auth/src/drivers/router/vue-router.2.x.js';
import router, { forceChangeRoute, Routes } from '@/router';
import Cookies from 'js-cookie';
import axios from 'axios';

export const LOGIN_URL = '/api/auth/login';
export const LOGOUT_URL = '/api/auth/logout';
export const REFRESH_URL = '/api/auth/refresh';
export const TOKEN_EXPIRATION_TIME = 9.9;


export default createAuth({
   plugins: {
      http: axios,
      router: router
   },
   drivers: {
      http: driverHttpAxios,
      router: driverRouterVueRouter,

      auth: {
         request: function(req, token) {
            this.drivers.http.setHeaders.call(this, req, {
               Authorization: 'Bearer ' + token
            });
         },
         response: function(res) {
            // 429 - To many requests
            // 401 - Unauthenticated
            // 500 && REFRESH_URL - Token in blacklist
            if (
               res.status === 429 ||
               res.status === 401 ||
               (res.status === 500 && res.config.url === REFRESH_URL)
            ) {
               this.logout();
               forceChangeRoute({ name: Routes.Login });
            }

            // Если запрос на логин или обновление токена
            if (res.data && res.data.data && res.data.data.access_token) {
               this.token(null, res.data.data.access_token, false);
            }

            const headers = this.drivers.http.getHeaders.call(this, res);
            let token = headers.Authorization || headers.authorization;

            if (token) {
               token = token.split(/Bearer:?\s?/i);

               return token[token.length > 1 ? 1 : 0].trim();
            }
         }
      }

   },
   options: {

      tokenDefaultKey: 'access_token',
      // staySignedInKey: 'access_token',
      // staySignedInKey: 'auth_stay_signed_in',
      refreshTokenKey: 'access_token',

      // stores: ['storage'],
      // stores: ['storage', 'cookie'],
      stores: ['cookie'],
      // stores: ['cookie', 'storage'],
      cookie: {
         Path: '/',
         Domain: null,
         Secure: false,
         Expires: 12096e5,
         SameSite: 'Lax'

      },

      rolesKey: 'type',
      notFoundRedirect: { name: Routes.NotFound },
      // authRedirect: { name: Routes.Login },

      refreshData: {
         url: REFRESH_URL,
         method: 'POST',
         staySignedIn: true,
         enabled: true,
         interval: TOKEN_EXPIRATION_TIME
      },

      // Здесь отключаем запрос user при заходе в новую вкладку
      fetchData: {
         url: 'api/auth/me',
         method: 'GET',
         enabled: false
      }

   }
});
