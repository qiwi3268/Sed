import store, { RootState } from '@/store';
import { ActionContext } from 'vuex';
import { GeCades } from '@/modules/signatures/GeCades';
import { LogicError } from '@/modules/errors/LogicError';
import { Api } from '@/modules/api/Api';
import { GeFile } from '@/store/modules/modals/files/uploader';

export enum SignState {
   Checking = 'checking',
   NotSigned = 'notSigned',
   Valid = 'green',
   Warning = 'orange',
   Invalid = 'red'
}

export type CspPlugin = {
   isInitialized?: boolean
   version?: string
   cspVersion?: string
}

/**
 * Описывает объект с информацией о сертификате
 */
export type Cert = {
   /**
    * Владелец сертификата и дата выдачи
    */
   text: string

   /**
    * Отпечаток сертификата
    */
   thumb: string
}

/**
 * Описывает объект с детальной информацией о сертификате
 */
export type CertInfo = {
   /**
    * Владелец
    */
   readonly subjectName: string

   /**
    * Издатель
    */
   readonly issuerName: string

   /**
    * Дата выдачи
    */
   readonly validFromDate: string

   /**
    * Срок действия
    */
   readonly validToDate: string

   /**
    * Описание статуса валидности
    */
   readonly certMessage: string

   /**
    * Статус валидности
    */
   readonly certStatus: boolean
}

export type FileSignerState = {
   opened: boolean
   isSigning: boolean

   plugin: CspPlugin
   certs: Cert[]
   selectedCert: Cert | null
   selectedCertInfo: CertInfo | null
   file: GeFile | null
}

type Context = ActionContext<FileSignerState, RootState>

export default {
   namespaced: true,
   state: (): FileSignerState => ({
      opened: false,
      isSigning: false,
      plugin: {
         isInitialized: false
      },
      certs: [],
      file: null,
      selectedCert: null,
      selectedCertInfo: null

   }),
   mutations: {},
   actions: {
      open({ state }: Context, file: GeFile): void {
         state.opened = true;
         state.file = file;
      },
      close({ state }: Context): void {
         state.opened = false;
         state.file = null;
      },
      initializePlugin({ state }: Context): Promise<void> {
         // Берем объект плагина
         return new Promise<void>(resolve => {
            GeCades.getCadesPlugin()
               // Получаем информацию о версии плагина
               .then(() => GeCades.getPlugin())
               // Получаем сертификаты пользователя
               .then((plugin: CspPlugin) => {
                  state.plugin = plugin;
                  return GeCades.getCerts();
               })
               // Добавляем блок с сертификатами
               .then((certs: Cert[]) => {
                  state.certs = certs;
                  state.plugin.isInitialized = true;
                  resolve();
               });
         });
      },
      selectCert({ state }: Context, cert: Cert): void {
         state.selectedCert = cert;
         GeCades.getCertInfo()
            // Добавляем на страницу данные о выбранном сертификате
            .then((certInfo: CertInfo) => {
               state.selectedCertInfo = certInfo;
               return GeCades.getSelectedCertificateAlgorithm();
            })
            .catch((exc: string) => {
               throw new LogicError(`Ошибка при получении информации о сертификате: ${exc}`);
            });
      },
      createSign({ state, getters, dispatch }: Context): Promise<void> {
         return new Promise(resolve => {
            state.isSigning = true;
            let selectedAlgorithm: string;

            GeCades.getSelectedCertificateAlgorithm()
               .then((algorithm: string) => {
                  selectedAlgorithm = algorithm;
                  return Api.getFileHash(algorithm, getters.getFile.starPath);
               })
               .then((fileHash: string) => GeCades.getSignHash(selectedAlgorithm, fileHash))
               .then((signHash: string) => {
                  const signBlob = new Blob([signHash], { type: 'text/plain' });
                  const signFile = new File([signBlob], `${getters.getFile.originalName}.sig`);
                  return dispatch('validateExternalSign', signFile);
               })
               .then(() => {
                  resolve();
                  state.isSigning = false;
               })
               .catch(() => state.isSigning = false);
         });
      },
      uploadExternalSignFiles({ state, dispatch }: Context, fileList: FileList): Promise<void> {
         return new Promise(resolve => {
            if (fileList && fileList.length > 0) {
               state.isSigning = true;
               const signFile = fileList.item(0);
               dispatch('validateExternalSign', signFile)
                  .then(() => {
                     resolve();
                     state.isSigning = false;
                  })
                  .catch(() => state.isSigning = false);
            }
         });
      },
      validateExternalSign({ state, getters }: Context, signFile: File): Promise<boolean | void> {
         return Api.validateExternalSign(getters.getFile.starPath, signFile)
            .then(response => {
               getters.getFile.validationResult = response.validationResult.signers;
               getters.getFile.signState = response.validationResult.result;
               getters.getFile.signStarPath = response.starPath;
            });
      }

   },
   getters: {
      getFile(state: FileSignerState): GeFile | null {
         return state.file;
      }
   }

};
