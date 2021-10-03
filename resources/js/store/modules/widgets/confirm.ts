import { ActionContext } from 'vuex';
import { RootState } from '@/store';
import { LogicError } from '@/modules/errors/LogicError';

export type ConfirmOptions = {
   message?: string
   target?: EventTarget
   group?: string
   icon?: string
   header?: string
   accept?: Function
   reject?: Function
   acceptLabel?: string
   rejectLabel?: string
   acceptIcon?: string
   rejectIcon?: string
   blockScroll?: boolean
   acceptClass?: string
   rejectClass?: string
}

export interface Confirm {
   require(args: ConfirmOptions): void
   close(): void
}

export type ConfirmState = {
   confirm: Confirm | null
}

type Context = ActionContext<ConfirmState, RootState>

export default {
   namespaced: true,
   state: (): ConfirmState => ({
      confirm: null
   }),
   mutations: {},
   actions: {
      openConfirm({ state }: Context, options: ConfirmOptions): void {
         if (!state.confirm) {
            throw new LogicError('Объект диалогового окна подтверждения не проинициализирован');
         }

         state.confirm.require(options);
      },
      setConfirm({ state }: Context, confirm: Confirm): void {
         state.confirm = confirm;
      }
   },
   getters: {
   }

};
