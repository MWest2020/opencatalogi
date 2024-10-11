/* eslint-disable no-console */
import { organization } from './organization'
import { mockorganization } from './organization.mock'

describe('organization Store', () => {
	it('create organization entity with full data', () => {
		const organization = new organization(mockorganization()[0])

		expect(organization).toBeInstanceOf(organization)
		expect(organization).toEqual(mockorganization()[0])

		expect(organization.validate().success).toBe(true)
	})

	it('create organization entity with partial data', () => {
		const organization = new organization(mockorganization()[1])

		expect(organization).toBeInstanceOf(organization)
		expect(organization.id).toBe(mockorganization()[1].id)
		expect(organization.title).toBe(mockorganization()[1].title)
		expect(organization.summary).toBe(mockorganization()[1].summary)
		expect(organization.description).toBe(mockorganization()[1].description)
		expect(organization.oin).toBe('')
		expect(organization.tooi).toBe('')
		expect(organization.rsin).toBe('')
		expect(organization.pki).toBe('')

		expect(organization.validate().success).toBe(true)
	})

	it('create organization entity with falsy data', () => {
		const organization = new organization(mockorganization()[2])

		expect(organization).toBeInstanceOf(organization)
		expect(organization).toEqual(mockorganization()[2])

		expect(organization.validate().success).toBe(false)
	})
})
