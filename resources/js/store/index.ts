import { createStore } from 'vuex';
import createPersistedState from 'vuex-persistedstate';
import createMutationsSharer from 'vuex-shared-mutations';
import user, { UserState } from '@/store/modules/user';
import auth, { AuthState } from '@/store/modules/auth';
import filesUploader, { FilesUploaderState } from '@/store/modules/modals/files/uploader';
import fileSigner, { FileSignerState } from '@/store/modules/modals/files/signer';
import errorModal, { ErrorState } from '@/store/modules/modals/error';
import signSessions, { SignSessionState } from '@/store/modules/sign-sessions/sign-sessions';
import organization, { OrganizationState } from '@/store/modules/organization';
import files, { FilesState } from '@/store/modules/files';
import toast, { ToastState } from '@/store/modules/widgets/toast';
import confirm, { ConfirmState } from '@/store/modules/widgets/confirm';
import polls, { PollState } from '@/store/modules/polls/polls';
import vacations, { VacationState } from '@/store/modules/vacations/vacations';
import conferences, { ConferencesState } from '@/store/modules/conferences/conferences';
import misc, { MiscState } from '@/store/modules/misc';
import navigation, { NavigationState } from '@/store/modules/navigation';
import birthdays, { BirthdaysState } from '@/store/modules/birthdays/birthdays';
// import Cookies from 'js-cookie';


export interface RootState {
   user: UserState
   auth: AuthState

   misc: MiscState
   files: FilesState
   navigation: NavigationState

   filesUploader: FilesUploaderState
   fileSigner: FileSignerState

   errorModal: ErrorState
   toast: ToastState
   confirm: ConfirmState

   organization: OrganizationState

   signSessions: SignSessionState
   polls: PollState
   vacations: VacationState
   conferences: ConferencesState
   birthdays: BirthdaysState

}

const store = createStore({
   plugins: [
      createPersistedState({
         paths: ['user']
      }),
      createMutationsSharer({ predicate: ['user/setUser'] })
   ],
   modules: {
      user,
      auth,

      misc,
      files,
      navigation,

      filesUploader,
      fileSigner,

      errorModal,
      toast,
      confirm,

      organization,

      signSessions,
      polls,
      vacations,
      conferences,
      birthdays
   },
   state: <RootState>{},
   mutations: {},
   actions: {},
   getters: {

   }
});

export default store;
