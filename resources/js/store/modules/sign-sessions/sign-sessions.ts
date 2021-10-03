import { inputMaxLength, mRequired, unique, ValidationConfig } from '@/modules/forms/validators';
import { Api } from '@/modules/api/Api';
import { ActionContext } from 'vuex';
import store, { RootState } from '@/store';
import router, { forceChangeRoute } from '@/router';
import { Toast } from '@/modules/notification/Toast';
import {
   SignSession,
   SignSessionDetails,
   SignSessionRead
} from '@/types/sign-sessions';
import { actionGuard, handleSuccess } from '@/modules/api/wrappers';
import { getSignSessionNavigationRoute } from '@/router/sign-sessions';

export type SignSessionState = {
   signSessionRead: SignSessionRead | null
   signSessionSigning: SignSessionDetails | null
   canDeleteSignSession: boolean | null
}

type Context = ActionContext<SignSessionState, RootState>

export default {
   namespaced: true,
   state: (): SignSessionState => ({
      signSessionRead: null,
      signSessionSigning: null,
      canDeleteSignSession: null
   }),
   mutations: {
   },
   actions: {
      async saveSignSession({ getters }: Context, model: SignSession): Promise<void> {
         await handleSuccess(Api.saveSignSession(model), Toast.savedSignSession);
         forceChangeRoute(getSignSessionNavigationRoute());
      },
      async fetchSignSessionRead({ state }: Context, signSessionId: string): Promise<void> {
         state.signSessionRead = null;
         state.canDeleteSignSession = null;

         state.signSessionRead = await Api.getSignSessionRead(signSessionId);
         state.canDeleteSignSession = await actionGuard(Api.canUserDeleteSignSession(signSessionId));
      },
      async deleteSignSession({ getters }: Context, model: SignSessionRead): Promise<void> {
         await handleSuccess(Api.deleteSignSession(model.uuid), Toast.signSessionDeleted);
         forceChangeRoute(getters.getSelectedViewRoute);
      },
      fetchSignSessionSigning({ state }: Context, signSessionId: string): Promise<SignSessionDetails> {
         state.signSessionSigning = null;
         return Api.getSignSessionSigning(signSessionId)
            .then(response => state.signSessionSigning = response);
      },
      async saveSignSessionSigning({ getters }: Context, model: SignSessionDetails): Promise<void> {
         await handleSuccess(Api.saveSignSessionSigning(model), Toast.signSessionSigned);
         forceChangeRoute(getSignSessionNavigationRoute());
      },
      returnToSelectedView(): void {
         router.push(getSignSessionNavigationRoute());
      },
      downloadSigns(context: Context, signSession: SignSessionRead): void {
         if (signSession.zipArchiveStarPath !== null) {
            store.dispatch('files/downloadFile', signSession.zipArchiveStarPath);
         } else {
            const searchParams = new URLSearchParams({ signatureSessionUuid: signSession.uuid });
            location.href = `${location.origin}/files/signatureSessions/downloadGeneratedZip?${searchParams}`;
         }
      }
   },
   getters: {
      getSignSessionEmptyForm(): SignSession {
         return {
            title: null,
            m1m1m1: [],
            signerIds: []
         };
      },
      getSignSessionRules(): ValidationConfig {
         return {
            title: { mRequired, inputMaxLength },
            m1m1m1: { mRequired },
            signerIds: { mRequired, unique }
         };
      }
   }
};
