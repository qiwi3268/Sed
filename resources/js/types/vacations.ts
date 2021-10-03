import { MiscItem } from '@/store/modules/misc';
import { FioResponse } from '@/types/api';

export type VacationCommonData = {
   employee: MiscItem | null
   startAt: Date | null
   duration: number
   replacementEmployees: MiscItem[]
}
export type VacationForm = VacationCommonData & {
   key: string | null
}
export type VacationFormEdit = VacationForm & {
   vacationId: number
   changed: boolean
}
export type VacationRead = VacationCommonData & {
   finishedAt: Date
   goingToWorkAt: Date
}

export enum VacationsReadViews {
   ForYearAndMonth,
   ForNext30Days
}

export enum VacationManagementCategories {
   Next = 'next',
   Past = 'past'
}

export type VacationChanges = {
   vacationsToSave: VacationForm[]
   vacationsToDelete: VacationFormEdit[]
}
export type VacationFormResponse = {
   id: number
   start_at: string
   duration: number
   user: FioResponse & {
      id: number
   }
   replacements: Array<FioResponse & {
      id: number
   }>
}
export type VacationReadResponse = VacationFormResponse & {
   finish_at: string
   going_to_work_at: string
}
