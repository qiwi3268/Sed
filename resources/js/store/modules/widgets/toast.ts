import { ActionContext } from 'vuex';
import { RootState } from '@/store';
import { LogicError } from '@/modules/errors/LogicError';

export enum ToastSeverity {
   Info = 'info',
   Error = 'error',
   Success = 'success'
}

export type ToastOptions = {
   severity?: ToastSeverity
   summary?: string
   detail?: string
   life?: number | null
   closable?: boolean
   group?: string
}

export interface Toast {
   add(args: ToastOptions): void
   removeGroup(group: string): void
   removeAllGroups(): void
}

export type ToastState = {
   toast: Toast | null
}

type Context = ActionContext<ToastState, RootState>

export default {
   namespaced: true,
   state: (): ToastState => ({
      toast: null
   }),
   mutations: {},
   actions: {
      addToast({ state }: Context, { severity = ToastSeverity.Error, summary, detail, closable, group, life = 3000 }: ToastOptions): void {
         if (!state.toast) {
            throw new LogicError('Объект всплывающей подсказки не проинициализирован');
         }

         const options = { severity, summary, detail, life, closable, group };
         state.toast.add(options);
      },
      setToast({ state }: Context, toast: Toast): void {
         state.toast = toast;
      }

   },
   getters: {
   }

};
