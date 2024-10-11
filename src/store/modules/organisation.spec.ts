/* eslint-disable no-console */
import { createPinia, setActivePinia } from 'pinia'

import { mockorganization, organization } from '../../entities/index.js'
import { useorganizationStore } from './organization'

describe('organization Store', () => {
	beforeEach(() => {
		setActivePinia(createPinia())
	})

	it('sets organization item correctly', () => {
		const store = useorganizationStore()

		store.setorganizationItem(mockorganization()[0])

		expect(store.organizationItem).toBeInstanceOf(organization)
		expect(store.organizationItem).toEqual(mockorganization()[0])
		expect(store.organizationItem.validate().success).toBe(true)

		store.setorganizationItem(mockorganization()[1])

		expect(store.organizationItem).toBeInstanceOf(organization)
		expect(store.organizationItem).toEqual(mockorganization()[1])
		expect(store.organizationItem.validate().success).toBe(true)

		store.setorganizationItem(mockorganization()[2])

		expect(store.organizationItem).toBeInstanceOf(organization)
		expect(store.organizationItem).toEqual(mockorganization()[2])
		expect(store.organizationItem.validate().success).toBe(false)
	})

	it('sets organization list correctly', () => {
		const store = useorganizationStore()

		store.setorganizationList(mockorganization())

		expect(store.organizationList).toHaveLength(mockorganization().length)

		expect(store.organizationList[0]).toBeInstanceOf(organization)
		expect(store.organizationList[0]).toEqual(mockorganization()[0])
		expect(store.organizationList[0].validate().success).toBe(true)

		expect(store.organizationList[1]).toBeInstanceOf(organization)
		expect(store.organizationList[1]).toEqual(mockorganization()[1])
		expect(store.organizationList[1].validate().success).toBe(true)

		expect(store.organizationList[2]).toBeInstanceOf(organization)
		expect(store.organizationList[2]).toEqual(mockorganization()[2])
		expect(store.organizationList[2].validate().success).toBe(false)
	})
})
