import { MiscItem } from '@/store/modules/misc';
import { FioResponse, PersonMiscItemResponse } from '@/types/api';

export enum ConferenceMiscs {
   Form = 'conferenceForm',
   Location = 'conferenceLocation',
   VksConnectionResponsible = 'VksConnectionResponsible',
   Members = 'Members'
}

export enum ConferenceForms {
   Intramural = 1,
   Vks = 2,
   VksIntramural = 3
}

export enum ConferenceStatuses {
   Planned = 'Запланировано',
   Finished = 'Состоялось'
}

export enum ConferenceViews {
   MyTodays = 'my/todays',
   MyPlanned = 'my/planned',
   ForDate = 'all/forDate',
}

export type ConferenceForm = {
   topic: string | null
   startAt: Date | null
   conferenceForm: MiscItem | null
   vksHref: string | null
   vksConnectionResponsible: MiscItem | null
   conferenceLocation: MiscItem | null
   members: MiscItem[]
   outerMembers: string | null
   comment: string | null
}
export type ConferenceFormEdit = ConferenceForm & {
   uuid: string
}
export type ConferenceResponse = {
   id: number
   uuid: string
   topic: string
   start_at: string
   created_at: string
   conference_form: string
   vks_href: string | null
   vks_connection_responsible_first_name: string | null
   vks_connection_responsible_last_name: string | null
   vks_connection_responsible_middle_name: string | null
   conference_location: string
   members: Array<FioResponse & {
      conference_id: number
   }>
   outer_members: string | null
   comment: string | null
}
export type ConferenceRead = {
   uuid: string
   topic: string
   startAt: Date
   conferenceForm: string
   vksHref: string | null
   connectionResponsible: string | null
   conferenceLocation: string
   members: string[]
   outerMembers: string | null
   comment: string | null
}
export type ConferenceEditResponse = {
   uuid: string
   topic: string
   start_at: string
   created_at: string
   conference_form: MiscItem
   vks_href: string | null
   vks_connection_responsible: PersonMiscItemResponse
   conference_location: MiscItem
   members: Array<FioResponse & {
      id: number
   }>
   outer_members: string | null
   comment: string | null
}
