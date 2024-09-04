import { SafeParseReturnType, z } from 'zod'
import { TOrganisation } from './organisation.types'

export class Organisation implements TOrganisation {

	public id: string
	public title: string
	public summary: string
	public description: string
	public oin: string
	public tooi: string
	public rsin: string
	public pki: string

	constructor(data: TOrganisation) {
		this.hydrate(data)
	}

	/* istanbul ignore next */ // Jest does not recognize the code coverage of these 2 methods
	private hydrate(data: TOrganisation) {
		this.id = data?.id?.toString() || ''
		this.title = data?.title || ''
		this.summary = data?.summary || ''
		this.description = data?.description || ''
		this.oin = data?.oin || ''
		this.tooi = data?.tooi || ''
		this.rsin = data?.rsin || ''
		this.pki = data?.pki || ''
	}

	/* istanbul ignore next */
	public validate(): SafeParseReturnType<TOrganisation, unknown> {
		// https://conduction.stoplight.io/docs/open-catalogi/ewlydzkylhygj-create-organisation
		const schema = z.object({
			title: z.string().min(1, 'is verplicht'),
			summary: z.string().min(1, 'is verplicht'),
			description: z.string(),
			// This regex could very well be faulty, as there is not any public information on HOW the OIN is made
			// before you tell me to fix it, tell me the correct OIN format
			// this is also true for the rest
			oin: z.string().regex(/^0000000\d{10}000$/, 'is niet een geldige OIN nummer').or(z.literal('')),
			tooi: z.string().regex(/^\d{1,}$/, 'is niet een geldige TOOI nummer').or(z.literal('')),
			rsin: z.string().regex(/^\d{9}$/, 'is niet een geldige RSIN nummer').or(z.literal('')),
			pki: z.string().regex(/^\d{1,}$/, 'is niet een geldige PKI nummer').or(z.literal('')),
		})

		const result = schema.safeParse({
			...this,
		})

		return result
	}

}
