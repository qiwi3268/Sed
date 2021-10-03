export type FioResponse = {
   readonly first_name: string
   readonly last_name: string
   readonly middle_name: string
}

export type UserResponse = FioResponse & {
   readonly uuid: string
}

export type FileFieldRules = {
   readonly maxSize: number
   readonly allowableExtensions: string[] | null
   readonly forbiddenSymbols: string[]
   readonly multipleAllowed: boolean
}

export type PersonMiscItemResponse = {
   id: number | null
   last_name: string | null
   first_name: string | null
   middle_name: string | null
}

export type PaginationResponse = {
   readonly per_page: number
   readonly current_page: number
   readonly last_page: number
   readonly links: Array<{
      readonly url: string
      readonly label: string
      readonly active: boolean
   }>
   readonly total: number
}

export type ViewPagination = {
   readonly perPage: number
   readonly currentPage: number
   readonly lastPage: number
   readonly links: Array<{
      readonly url: string | null
      readonly label: string
      readonly active: boolean
   }>
   readonly total: number
}
