import { JsonParams } from '@/modules/api/ApiHandler';
import { formatMiscItem, formatToJsonDateTime, parseDate } from '@/modules/forms/formatting';
import { Fio } from '@/modules/utils/Fio';
import {
   ConferenceEditResponse,
   ConferenceForm,
   ConferenceFormEdit,
   ConferenceRead,
   ConferenceResponse
} from '@/types/conferences';

export const transformConference = (form: ConferenceForm): JsonParams => {
   return {
      topic: form.topic,
      startAt: formatToJsonDateTime(form.startAt!),
      conferenceForm: formatMiscItem(form.conferenceForm),
      vksHref: form.vksHref,
      vksConnectionResponsibleId: formatMiscItem(form.vksConnectionResponsible),
      conferenceLocation: formatMiscItem(form.conferenceLocation),
      memberIds: formatMiscItem(form.members),
      outerMembers: form.outerMembers,
      comment: form.comment
   };
};

export const transformConferenceEdit = (form: ConferenceFormEdit): JsonParams => {
   return {
      conferenceUuid: form.uuid,
      ...transformConference(form)
   };
};

export const transformConferenceResponse = (conference: ConferenceResponse): ConferenceRead => {
   return {
      uuid: conference.uuid,
      topic: conference.topic,
      startAt: parseDate(conference.start_at),
      conferenceForm: conference.conference_form,
      vksHref: conference.vks_href,
      connectionResponsible: getConnectionResponsible(conference),
      conferenceLocation: conference.conference_location,
      members: conference.members.map(member => new Fio(member.last_name, member.first_name, member.middle_name).getShortFio()),
      outerMembers: conference.outer_members,
      comment: conference.comment
   };
};

export const getConnectionResponsible = (data: ConferenceResponse): string | null => {
   let result: string | null = null;

   const lastName = data.vks_connection_responsible_last_name;
   const firstName = data.vks_connection_responsible_first_name;
   const middleName = data.vks_connection_responsible_middle_name;
   if (lastName && firstName) {
      result = new Fio(lastName, firstName, middleName).getShortFio();
   }

   return result;
};

export const transformConferenceEditResponse = (conference: ConferenceEditResponse): ConferenceFormEdit => {
   const result: ConferenceFormEdit = {
      uuid: conference.uuid,
      topic: conference.topic,
      startAt: parseDate(conference.start_at),
      conferenceForm: conference.conference_form,
      vksHref: conference.vks_href,
      vksConnectionResponsible: null,
      conferenceLocation: conference.conference_location,
      members: conference.members.map(member => ({
         id: member.id,
         label: new Fio(member.last_name, member.first_name, member.middle_name).getLongFio()
      })),
      outerMembers: conference.outer_members,
      comment: conference.comment
   };

   const responsible = conference.vks_connection_responsible;
   if (responsible.id) {
      result.vksConnectionResponsible = {
         id: responsible.id,
         label: new Fio(responsible.last_name, responsible.first_name, responsible.middle_name).getLongFio()
      };
   }

   return result;
};
