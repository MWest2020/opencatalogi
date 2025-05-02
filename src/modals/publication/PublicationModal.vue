<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcDialog v-if="navigationStore.modal === 'publication'"
		:name="isEdit ? 'Bewerk Publicatie' : 'Publicatie toevoegen'"
		size="normal"
		:can-close="false">
		<div v-if="objectStore.getState('publication').success !== null || objectStore.getState('publication').error">
			<NcNoteCard v-if="objectStore.getState('publication').success" type="success">
				<p>{{ isEdit ? 'Publicatie succesvol bewerkt' : 'Publicatie succesvol aangemaakt' }}</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState('publication').success" type="error">
				<p>{{ isEdit ? 'Er is iets fout gegaan bij het bewerken van Publicatie' : 'Er is iets fout gegaan bij het aanmaken van Publicatie' }}</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState('publication').error" type="error">
				<p>{{ objectStore.getState('publication').error }}</p>
			</NcNoteCard>
		</div>

		<template #actions>
			<NcButton
				@click="closeModal">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ objectStore.getState('publication').success ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/publicaties', '_blank')">
				<template #icon>
					<Help :size="20" />
				</template>
				Help
			</NcButton>
			<NcButton v-if="objectStore.getState('publication').success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || objectStore.isLoading('publication')"
				type="primary"
				@click="savePublication">
				<template #icon>
					<NcLoadingIcon v-if="objectStore.isLoading('publication')" :size="20" />
					<ContentSaveOutline v-if="!objectStore.isLoading('publication')" :size="20" />
				</template>
				{{ isEdit ? 'Opslaan' : 'Toevoegen' }}
			</NcButton>
		</template>

		<div class="formContainer">
			<div v-if="!isEdit">
				<div v-if="catalogi?.value?.id" class="editParameterContainer">
					<b>Catalogus:</b> {{ catalogi.value.label }}
					<NcButton @click="catalogi.value = null">
						<Pencil :size="20" />
					</NcButton>
				</div>
				<div v-if="publicationType?.value?.id" class="editParameterContainer">
					<b>Publicatietype:</b> {{ publicationType.value.label }}
					<NcButton @click="publicationType.value = null">
						<Pencil :size="20" />
					</NcButton>
				</div>
			</div>

			<div v-if="objectStore.getState('publication').success === null" class="form-group">
				<!-- STAGE 1 - Only for new publications -->
				<div v-if="!isEdit && !catalogi?.value?.id">
					<p>
						Publicaties horen in een
						<a
							class="definitionLink"
							@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/catalogi', '_blank')">
							catalogus</a>, aan welke catlogus wilt u deze publicatie toevoegen?
					</p>
					<NcSelect v-bind="catalogi"
						v-model="catalogi.value"
						input-label="Catalogus*"
						:loading="catalogiLoading"
						:disabled="objectStore.isLoading('publication')"
						required />
				</div>

				<!-- STAGE 2 - Only for new publications -->
				<div v-if="!isEdit && catalogi?.value?.id && !publicationType?.value?.id">
					<p>
						Publicaties worden gedefineerd door
						<a
							class="definitionLink"
							@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/publication-types', '_blank')">
							publicatietypes</a>, van welk publicatietype wit u een publicatie aanmaken?
					</p>
					<div v-if="!filteredPublicationTypeOptions.options?.length">
						<p>
							<strong>Er zijn nog geen publicatietypes toegevoegd aan deze Catalogus.</strong>
						</p>
						<p>
							<strong>Voeg een publicatietype toe om een publicatie aan te maken.</strong>
						</p>
					</div>
					<div v-if="filteredPublicationTypeOptions.options?.length > 0">
						<NcSelect v-bind="filteredPublicationTypeOptions"
							v-model="publicationType.value"
							input-label="Publicatietype*"
							:loading="publicationTypeLoading"
							:disabled="publicationTypeLoading || objectStore.isLoading('publication')"
							required />
					</div>
				</div>

				<!-- STAGE 3 - Form fields for both new and edit -->
				<div v-if="isEdit || (catalogi.value?.id && publicationType.value?.id)" class="formContainer">
					<NcTextField :disabled="objectStore.isLoading('publication')"
						label="Titel*"
						:value.sync="publication.title"
						:error="!!inputValidation.fieldErrors?.['title']"
						:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
					<NcTextField :disabled="objectStore.isLoading('publication')"
						label="Samenvatting*"
						required
						:value.sync="publication.summary"
						:error="!!inputValidation.fieldErrors?.['summary']"
						:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
					<NcTextArea :disabled="objectStore.isLoading('publication')"
						label="Beschrijving"
						:value.sync="publication.description"
						:error="!!inputValidation.fieldErrors?.['description']"
						:helper-text="inputValidation.fieldErrors?.['description']?.[0]"
						resize="vertical" />
					<NcTextField :disabled="objectStore.isLoading('publication')"
						label="Reference"
						:value.sync="publication.reference"
						:error="!!inputValidation.fieldErrors?.['reference']"
						:helper-text="inputValidation.fieldErrors?.['reference']?.[0]" />
					<NcTextField :disabled="objectStore.isLoading('publication')"
						label="Categorie"
						:value.sync="publication.category"
						:error="!!inputValidation.fieldErrors?.['category']"
						:helper-text="inputValidation.fieldErrors?.['category']?.[0]" />
					<NcTextField :disabled="objectStore.isLoading('publication')"
						label="Portaal"
						:value.sync="publication.portal"
						:error="!!inputValidation.fieldErrors?.['portal']"
						:helper-text="inputValidation.fieldErrors?.['portal']?.[0]" />
					<span>
						<p>Publicatie datum</p>
						<NcDateTimePicker v-model="publication.published"
							:disabled="objectStore.isLoading('publication')"
							label="Publicatie datum" />
					</span>
					<span class="APM-horizontal">
						<NcCheckboxRadioSwitch :disabled="objectStore.isLoading('publication')"
							label="Featured"
							:checked.sync="publication.featured">
							Uitgelicht
						</NcCheckboxRadioSwitch>
					</span>
					<NcTextField :disabled="objectStore.isLoading('publication')"
						label="Image"
						:value.sync="publication.image"
						:error="!!inputValidation.fieldErrors?.['image']"
						:helper-text="inputValidation.fieldErrors?.['image']?.[0]" />
					<span class="inputContainer">
						<b>Juridisch</b>
						<NcTextField :disabled="objectStore.isLoading('publication')"
							label="Licentie"
							:value.sync="publication.license"
							:error="!!inputValidation.fieldErrors?.['license']"
							:helper-text="inputValidation.fieldErrors?.['license']?.[0]" />
					</span>
					<NcSelect v-bind="organizations"
						v-model="organizations.value"
						input-label="Organisatie"
						:loading="organizationsLoading"
						:disabled="objectStore.isLoading('publication')" />
				</div>
			</div>
		</div>
	</NcDialog>
</template>

<script>
import {
	NcButton,
	NcDialog,
	NcTextField,
	NcTextArea,
	NcSelect,
	NcLoadingIcon,
	NcCheckboxRadioSwitch,
	NcNoteCard,
	NcDateTimePicker,
} from '@nextcloud/vue'

import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import Help from 'vue-material-design-icons/Help.vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'

import { Publication } from '../../entities/index.js'

export default {
	name: 'PublicationModal',
	components: {
		NcDialog,
		NcTextField,
		NcTextArea,
		NcButton,
		NcSelect,
		NcLoadingIcon,
		NcCheckboxRadioSwitch,
		NcNoteCard,
		NcDateTimePicker,
		// Icons
		ContentSaveOutline,
		Help,
		Cancel,
		Pencil,
	},
	data() {
		return {
			publication: {
				title: '',
				summary: '',
				description: '',
				reference: '',
				license: '',
				featured: false,
				portal: '',
				category: '',
				published: '',
				image: '',
				data: {},
			},
			organizations: {
				value: null,
				options: [],
			},
			catalogi: {
				value: null,
				options: [],
			},
			publicationType: {
				value: null,
				options: [],
			},
			catalogiLoading: false,
			publicationTypeLoading: false,
			organizationsLoading: false,
			hasUpdated: false,
		}
	},
	computed: {
		isEdit() {
			return !!objectStore.getActiveObject('publication')
		},
		filteredPublicationTypeOptions() {
			if (!this.catalogi?.value?.id) return {}
			if (!this.publicationType?.options?.length) return {}

			const selectedCatalogus = objectStore.getObject('catalog', this.catalogi.value.id)
			if (!selectedCatalogus) return {}

			const filteredPublicationType = objectStore.getCollection('publicationType').results
				.filter((publicationType) => {
					return selectedCatalogus.publicationTypes.includes(publicationType.id.toString())
				})

			return {
				options: filteredPublicationType.map((publicationType) => ({
					id: publicationType.id,
					source: publicationType.source,
					label: publicationType.title,
				})),
			}
		},
		inputValidation() {
			const publicationItem = new Publication({
				...this.publication,
				catalog: this.isEdit ? this.publication.catalog : this.catalogi.value?.id,
				publicationType: this.isEdit ? this.publication.publicationType : this.publicationType.value?.id,
				published: this.publication.published !== '' ? new Date(this.publication.published).toISOString() : new Date().toISOString(),
				organization: this.organizations.value?.id,
			})

			const result = publicationItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	updated() {
		if (navigationStore.modal === 'publication' && !this.hasUpdated) {
			this.hasUpdated = true
			if (this.isEdit) {
				const activePublication = objectStore.getActiveObject('publication')
				this.publication = { ...activePublication }
				this.fetchOrganizations()
			} else {
				this.fetchCatalogi()
				this.fetchPublicationTypes()
				this.fetchOrganizations()
			}
		}
	},
	methods: {
		fetchCatalogi() {
			this.catalogiLoading = true
			const data = objectStore.getCollection('catalog').results

			this.catalogi.options = data.map((catalog) => ({
				id: catalog.id,
				label: catalog.title,
			}))
			this.catalogiLoading = false

		},
		fetchPublicationTypes() {
			this.publicationTypeLoading = true
			const data = objectStore.getCollection('publicationType').results

			this.publicationType.options = data.map((type) => ({
				id: type.id,
				label: type.title,
			}))
			this.publicationTypeLoading = false
		},
		fetchOrganizations() {
			this.organizationsLoading = true
			const data = objectStore.getCollection('organization').results

			this.organizations.options = data.map((org) => ({
				id: org.id,
				label: org.title,
			}))
			if (this.isEdit) {
				const selectedOrg = data.find(org => org.id.toString() === this.publication.organization.toString())
				this.organizations.value = selectedOrg
					? {
						id: selectedOrg.id,
						label: selectedOrg.title,
					}
					: null
			}
			this.organizationsLoading = false

		},
		savePublication() {
			const publicationItem = new Publication({
				...this.publication,
				catalog: this.isEdit ? this.publication.catalog : this.catalogi.value?.id,
				publicationType: this.isEdit ? this.publication.publicationType : this.publicationType.value?.id,
				published: this.publication.published !== '' ? new Date(this.publication.published).toISOString() : new Date().toISOString(),
				organization: this.organizations.value?.id,
			})

			if (this.isEdit) {
				objectStore.updateObject('publication', publicationItem.id, publicationItem)
					.then(() => {
						// Wait for the user to read the feedback then close the model
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			} else {
				objectStore.createObject('publication', publicationItem)
					.then(() => {
						// Wait for the user to read the feedback then close the model
						const self = this
						setTimeout(function() {
							self.closeModal()
						}, 2000)
					})
			}
		},
		closeModal() {
			navigationStore.setModal(false)
			this.hasUpdated = false
			this.publication = {
				title: '',
				summary: '',
				description: '',
				reference: '',
				license: '',
				featured: false,
				portal: '',
				category: '',
				published: '',
				image: '',
				data: {},
			}
			this.catalogi = {
				value: null,
				options: [],
			}
			this.publicationType = {
				value: null,
				options: [],
			}
			this.organizations = {
				value: null,
				options: [],
			}
			// Reset the object store state
			objectStore.setState('publication', { success: null, error: null })
		},
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>

<style>
.modal__content {
  margin: var(--OC-margin-50);
  text-align: center;
}

.formContainer > * {
  margin-block-end: 10px;
}

.selectGrid {
  display: grid;
  grid-gap: 5px;
  grid-template-columns: 1fr 1fr;
}

.zaakDetailsContainer {
  margin-block-start: var(--OC-margin-20);
  margin-inline-start: var(--OC-margin-20);
  margin-inline-end: var(--OC-margin-20);
}

.success {
  color: green;
}

.APM-horizontal {
  display: flex;
  gap: 4px;
  flex-direction: row;
  align-items: center;
}

.definitionLink {
  transition: .2s;
  text-decoration: underline;
}
.definitionLink:hover {
  color: var(--color-primary-element);
}
</style>

<style scoped>
.editParameterContainer {
  display: flex;
  align-items: center;
  gap: 10px;
}

.formContainer {
  display: flex;
  flex-direction: column;
}

.inputContainer {
  display: flex;
  flex-direction: column;
  gap: 5px;
}
</style>
