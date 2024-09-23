<script setup>
import { metadataStore, navigationStore, publicationStore, catalogiStore } from '../../store/store.js'
</script>

<template>
	<NcDialog v-if="navigationStore.modal === 'publicationAdd'"
		name="Publicatie toevoegen"
		size="normal"
		:can-close="false">
		<div v-if="success !== null || error">
			<NcNoteCard v-if="success" type="success">
				<p>Publicatie succesvol aangemaakt</p>
			</NcNoteCard>
			<NcNoteCard v-if="!success" type="error">
				<p>Er is iets fout gegaan bij het aanmaken van Publicatie</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
		</div>

		<template #actions>
			<NcButton v-if="catalogi?.value?.id && !metaData?.value?.id"
				:disabled="loading"
				@click="catalogi.value = null">
				<template #icon>
					<ArrowLeft :size="20" />
				</template>
				Terug naar Catalogi
			</NcButton>
			<NcButton v-if="catalogi.value?.id && metaData.value?.id"
				:disabled="loading"
				@click="metaData.value = null">
				<template #icon>
					<ArrowLeft :size="20" />
				</template>
				Terug naar publicatie type
			</NcButton>
			<NcButton
				@click="closeModal">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/gebruikers/publicaties', '_blank')">
				<template #icon>
					<Help :size="20" />
				</template>
				Help
			</NcButton>
			<NcButton v-if="success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || loading"
				type="primary"
				@click="addPublication()">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>
		</template>

		<div class="formContainer">
			<div v-if="catalogi?.value?.id && success === null">
				<b>Catalogus:</b> {{ catalogi.value.label }}
				<NcButton @click="catalogi.value = null">
					Catalogus wijzigen
				</NcButton>
			</div>
			<div v-if=" metaData.value?.id && success === null">
				<b>Publicatietype:</b> {{ metaData.value.label }}
				<NcButton @click="metaData.value = null">
					Publicatietype wijzigen
				</NcButton>
			</div>
			<div v-if="success === null" class="form-group">
				<!-- STAGE 1 -->
				<div v-if="!catalogi?.value?.id">
					<p>Publicaties horen in een <a @click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/catalogi', '_blank')">catalogus</a>, aan welke catlogus wilt u deze publicatie toevoegen?</p>
					<NcSelect v-bind="catalogi"
						v-model="catalogi.value"
						input-label="Catalogus*"
						:loading="catalogiLoading"
						:disabled="publicationLoading"
						required />
				</div>
				<!-- STAGE 2 -->
				<div v-if="catalogi?.value?.id && !metaData?.value?.id">
					<p>Publicaties worden gedefineerd door <a @click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/metadata', '_blank')">publicatie typen</a>, van welk publicatie type wit u een publicatie aanmaken?</p>
					<div v-if="!filteredMetadataOptions.options?.length">
						<p>
							<strong>Er zijn nog geen publicatieTypes toegevoegd aan deze Catalogus.</strong>
						</p>
						<p>
							<strong>Voeg een publicatieType toe om een publicatie aan te maken.</strong>
						</p>
					</div>
					<div v-if="filteredMetadataOptions.options?.length > 0">
						<NcSelect v-bind="filteredMetadataOptions"
							v-model="metaData.value"
							input-label="Publicatie type*"
							:loading="metaDataLoading"
							:disabled="metaDataLoading || publicationLoading"
							required />
					</div>
				</div>
				<!-- STAGE 3 -->
				<div v-if="catalogi.value?.id && metaData.value?.id">
					<NcTextField :disabled="loading"
						label="Titel*"
						:value.sync="publication.title"
						:error="!!inputValidation.fieldErrors?.['title']"
						:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
					<NcTextField :disabled="loading"
						label="Samenvatting*"
						required
						:value.sync="publication.summary"
						:error="!!inputValidation.fieldErrors?.['summary']"
						:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
					<NcTextArea :disabled="loading"
						label="Beschrijving"
						:value.sync="publication.description"
						:error="!!inputValidation.fieldErrors?.['description']"
						:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
					<NcTextField :disabled="loading"
						label="Reference"
						:value.sync="publication.reference"
						:error="!!inputValidation.fieldErrors?.['reference']"
						:helper-text="inputValidation.fieldErrors?.['reference']?.[0]" />
					<NcTextField :disabled="loading"
						label="Categorie"
						:value.sync="publication.category"
						:error="!!inputValidation.fieldErrors?.['category']"
						:helper-text="inputValidation.fieldErrors?.['category']?.[0]" />
					<NcTextField :disabled="loading"
						label="Portaal"
						:value.sync="publication.portal"
						:error="!!inputValidation.fieldErrors?.['portal']"
						:helper-text="inputValidation.fieldErrors?.['portal']?.[0]" />
					<span>
						<p>Publicatie datum</p>
						<NcDateTimePicker v-model="publication.published"
							:disabled="loading"
							label="Publicatie datum" />
					</span>
					<span class="APM-horizontal">
						<NcCheckboxRadioSwitch :disabled="loading"
							label="Featured"
							:checked.sync="publication.featured">
							Uitgelicht
						</NcCheckboxRadioSwitch>
					</span>
					<NcTextField :disabled="loading"
						label="Image"
						:value.sync="publication.image"
						:error="!!inputValidation.fieldErrors?.['image']"
						:helper-text="inputValidation.fieldErrors?.['image']?.[0]" />
					<b>Juridisch</b>
					<NcTextField :disabled="loading"
						label="Licentie"
						:value.sync="publication.license"
						:error="!!inputValidation.fieldErrors?.['license']"
						:helper-text="inputValidation.fieldErrors?.['license']?.[0]" />
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

import Plus from 'vue-material-design-icons/Plus.vue'
import Help from 'vue-material-design-icons/Help.vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import ArrowLeft from 'vue-material-design-icons/ArrowLeft.vue'
import { Publication } from '../../entities/index.js'

export default {
	name: 'AddPublicationModal',
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
		Plus,
		Help,
		Cancel,
		ArrowLeft,
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
			catalogiList: [], // this is the entire dataset of catalogi
			catalogi: {},
			metaDataList: [], // this is the entire dataset of metadata
			metaData: {},
			errorCode: '',
			catalogiLoading: false,
			metaDataLoading: false,
			publicationLoading: false,
			hasUpdated: false,
			loading: false,
			success: null,
			error: false,
			dataIsValidJson: false,
			attachmentsIsValidJson: false,
		}
	},
	computed: {
		filteredMetadataOptions() {
			if (!this.catalogiList?.length) return {}
			if (!this.catalogi?.options?.length) return {}
			if (!this.catalogi?.value.id) return {}
			if (!this.metaDataList?.length) return {}

			// step 1: get the selected catalogus from the catalogi dropdown
			const selectedCatalogus = this.catalogiList
				.filter((catalogus) => catalogus.id.toString() === this.catalogi.value.id.toString())[0]

			// step 2: get the full metadata's from the metadataIds
			const filteredMetadata = this.metaDataList
				.filter((metadata) => selectedCatalogus.metadata.includes(metadata.source))

			return {
				options: filteredMetadata.map((metaData) => ({
					id: metaData.id,
					source: metaData.source,
					label: metaData.title,
				})),
			}
		},
		inputValidation() {
			const testClass = new Publication({
				...this.publication,
				catalogi: this.catalogi.value?.id,
				metaData: this.metaData.value?.source,
				published: this.publication.published.toISOString(),
				schema: 'https://sadanduseless.b-cdn.net/wp-content/uploads/2018/11/funny-cat-closeup3.jpg',
			})

			const result = testClass.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	watch: {
		data: {
			handler(data) {
				this.dataIsValidJson = this.isJsonString(data)
			},
			deep: true,
		},
		attachments: {
			handler(attachments) {
				this.attachmentsIsValidJson = this.isJsonString(attachments)
			},
			deep: true,
		},
	},
	updated() {
		if (navigationStore.modal === 'publicationAdd' && !this.hasUpdated) {

			this.fetchCatalogi()
			this.fetchMetaData()
			this.hasUpdated = true
		}
	},
	methods: {
		fetchCatalogi() {
			this.catalogiLoading = true

			catalogiStore.getAllCatalogi()
				.then(({ response, data }) => {

					this.catalogiList = data

					const selectedCatalogus = navigationStore.getTransferData() !== 'ignore selectedCatalogus'
						? data.filter((catalogus) => catalogus.id.toString() === navigationStore.selectedCatalogus.toString())[0]
						: null

					this.catalogi = {
						options: Object.entries(data).map((catalog) => ({
							id: catalog[1].id,
							label: catalog[1].title,
						})),
						value: selectedCatalogus
							? {
								id: selectedCatalogus.id,
								label: selectedCatalogus.title,
							}
							: null,
					}

					this.catalogiLoading = false
				})
				.catch((err) => {
					console.error(err)
					this.catalogiLoading = false
				})
		},
		fetchMetaData() {
			this.metaDataLoading = true

			metadataStore.getAllMetadata()
				.then(({ data }) => {
					this.metaDataList = data

					this.metaDataLoading = false
				})
		},
		isJsonString(str) {
			try {
				JSON.parse(str)
			} catch (e) {
				return false
			}
			return true
		},
		addPublication() {
			this.loading = true
			this.error = false

			const publicationItem = new Publication({
				...this.publication,
				catalogi: this.catalogi.value.id,
				metaData: this.metaData.value.source,
				published: this.publication.published.toISOString(),
				schema: 'https://sadanduseless.b-cdn.net/wp-content/uploads/2018/11/funny-cat-closeup3.jpg',
			})

			publicationStore.addPublication(publicationItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					navigationStore.setSelected('publication')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.closeModal()
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					this.hasUpdated = false
				})
		},
		closeModal() {
			this.success = null
			navigationStore.selectedCatalogus && navigationStore.setSelected('publication')
			navigationStore.setModal(false)
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
			this.catalogi = {}
			this.metaData = {}
			this.hasUpdated = false
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

.formContainer>* {
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

.apm-submit-button {
    margin-block-start: 1rem
}
</style>
