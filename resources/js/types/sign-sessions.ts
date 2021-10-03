import { GeFile } from '@/store/modules/modals/files/uploader';
import { MiscItem } from '@/store/modules/misc';
import { Validation, ValidationResponse } from '@/modules/api/handlers/files/ExternalSignValidation';
import { FioResponse, PaginationResponse } from '@/types/api';

export type SignSession = {
   title: string | null
   m1m1m1: GeFile[]
   signerIds: MiscItem[]
}

export enum SignerStates {
   Invalid = 'invalid',
   Valid = 'valid'
}

export type FinishedSigner = {
   readonly fio: string
   readonly signedAt: string
   readonly validationResult: Validation
   readonly signerState: SignerStates
}
export type UnfinishedSigner = {
   readonly fio: string
   readonly statusId: number
   readonly statusName: string
}
export type SignSessionSigners = {
   readonly signed: FinishedSigner[]
   readonly unsigned: UnfinishedSigner[]
}

export enum SignSessionStatuses {
   InWork = 1,
   Finished = 2
}

export enum SignSessionViewWindowSizes {
   Md = 1024,
   Sm = 600,
}

export type SignSessionCommonData = {
   readonly uuid: string
   readonly title: string
   readonly author: string
   readonly createdAt: string
}
export type SignSessionDetails = SignSessionCommonData & {
   readonly file: GeFile
   readonly statusId: SignSessionStatuses
   readonly statusName: string
}

export interface SignSessionRead extends SignSessionDetails {
   readonly id: string
   readonly signers: SignSessionSigners
   readonly zipArchiveStarPath: string | null
}

export enum SignSessionViews {
   WaitingAction = 'waitingAction',
   InWork = 'inWork',
   Finished = 'finished'
}

export type FinishedSignerResponse = FioResponse & {
   readonly signed_at: string
   readonly validation_result: ValidationResponse
}

type NotFinishedSignerResponse = FioResponse & {
   readonly status_id: number
   readonly status_name: string
}

export type SignSessionSignersResponse = {
   readonly signed: FinishedSignerResponse[]
   readonly unsigned: NotFinishedSignerResponse[]
}

export type SignSessionCommonDataResponse = {
   readonly uuid: string
   readonly title: string
   readonly created_at: string

   readonly author_last_name: string
   readonly author_first_name: string
   readonly author_middle_name: string
}

export type SignSessionResponse = SignSessionCommonDataResponse & {
   readonly id: string

   readonly file_original_name: string
   readonly file_size: number
   readonly file_star_path: string

   readonly status_id: number
   readonly status_name: string

   readonly zip_archive_star_path: string | null
}

export type SignSessionReadResponse = SignSessionResponse & {
   signers: SignSessionSignersResponse
}
export type SignSessionFinishedResponse = {
   data: Array<SignSessionCommonDataResponse & {
      readonly finished_at: string

      readonly signers: FioResponse[]
   }>
} & PaginationResponse

export type SignSessionFinished = {
   readonly uuid: string
   readonly title: string
   readonly createdAt: string
   readonly signedAt: string
   readonly author: string
   readonly signers: Array<{ readonly fio: string }>
}
export type SignSessionsInWorkResponse = {
   data: Array<SignSessionCommonDataResponse & {
      readonly signature_session_status_id: number
      readonly signature_session_status_name: string
      readonly signers: Array<FioResponse & {
         readonly signature_session_signer_status_id: number
      }>
   }>
} & PaginationResponse

export enum SignSessionSignerStatuses {
   InWork = 1,
   Signed = 2
}

export type SignSessionInWork = {
   readonly uuid: string
   readonly title: string
   readonly createdAt: string
   readonly author: string
   readonly status: {
      readonly id: number
      readonly name: string
   }
   readonly signers: Array<{
      readonly fio: string
      readonly signStatusId: SignSessionSignerStatuses
   }>
}
export type SignSessionsWaitingActionResponse = {
   readonly data: Array<SignSessionCommonDataResponse & {
      readonly signature_session_status_id: number
      readonly signature_session_status_name: string
   }>
} & PaginationResponse
export type SignSessionWaitingAction = {
   readonly uuid: string
   readonly title: string
   readonly author: string
   readonly createdAt: string
   readonly status: {
      readonly id: number
      readonly name: string
   }
}
