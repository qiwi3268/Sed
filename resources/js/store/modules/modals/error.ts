import { ActionContext } from 'vuex';
import { RootState } from '@/store';

export interface ErrorDescription {
   title: string
   messages: string[]
   code: string | null
}

export interface ErrorState extends ErrorDescription {
   opened: boolean
}

type Context = ActionContext<ErrorState, RootState>

export default {
   namespaced: true,
   state: (): ErrorState => ({
      opened: false,
      title: '',
      messages: [],
      code: null
   }),
   mutations: {},
   actions: {
      open({ state }: Context, { title, messages, code = null }: ErrorDescription): void {
         state.opened = true;
         state.title = title;
         state.messages = Array.isArray(messages) ? messages : [messages];
         state.code = code;
      },
      close({ state }: Context): void {
         state.opened = false;
         state.title = '';
         state.messages = [];
         state.code = null;
      }
   },
   getters: {
   }

};
