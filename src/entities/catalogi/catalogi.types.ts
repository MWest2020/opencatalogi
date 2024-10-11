export type TCatalogi = {
    id: string
    title: string
    summary: string
    description: string
    image: string
    listed: boolean
    organisation: string // it is supposed to be TOrganisation according to the stoplight, but reality is a bit different
    publicationType: string[]
}
