import { Torganization } from '../organization'

export type TCatalogi = {
    id: string
    title: string
    summary: string
    description: string
    image: string
    listed: boolean
    organization: string | Torganization // it is supposed to be Torganization according to the stoplight, but reality is a bit different
    metadata: string[]
}
