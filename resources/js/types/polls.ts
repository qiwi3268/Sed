import { FioResponse } from '@/types/api';

export type IconDescription = {
   name: string
   modifier: string
}
export type PollAtWorkOption = {
   id: number
   name: string
   icon: IconDescription
   users: string[]
}
export type PollAtWork = {
   createdAt: Date
   finishedAt: Date | null
   statusId: number
   statusName: string
   options: PollAtWorkOption[]
}
export type PollAtWorkUserResponse = FioResponse & {
   answer_id: number | null
   on_vacation: boolean | null
}
export type PollAtWorkUser = {
   fio: string
   optionId: number | null
   onVacation: boolean | null
}
export type PollAtWorkOptionResponse = {
   id: number
   name: string
   answer_id: number
}
export type PollAtWorkResponse = {
   options: PollAtWorkOptionResponse[]
   id: number
   created_at: string
   finished_at: string | null
   status_id: number
   status_name: string
   users: PollAtWorkUserResponse[]
}
