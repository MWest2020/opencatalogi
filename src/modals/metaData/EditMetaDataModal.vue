<script setup>
import { navigationStore, metadataStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'editMetaData'"
		ref="modalRef"
		label-id="editMetaDataModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Publicatie type bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie type succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van metadata</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success == null" class="form-group">
				<NcTextField
					label="Titel"
					:disabled="loading"
					:value.sync="metadata.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField
					label="Versie"
					:disabled="loading"
					:value.sync="metadata.version"
					:error="!!inputValidation.fieldErrors?.['version']"
					:helper-text="inputValidation.fieldErrors?.['version']?.[0]" />
				<NcTextField
					label="Samenvatting*"
					:disabled="loading"
					:value.sync="metadata.summary"
					:error="!!inputValidation.fieldErrors?.['summary']"
					:helper-text="inputValidation.fieldErrors?.['summary']?.[0]" />
				<NcTextArea
					label="Beschrijving"
					:disabled="loading"
					:value.sync="metadata.description"
					:error="!!inputValidation.fieldErrors?.['description']"
					:helper-text="inputValidation.fieldErrors?.['description']?.[0]" />
			</div>
			<NcButton
				v-if="success == null"
				:disabled="!inputValidation.success || loading"
				type="primary"
				@click="editMetaData">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<ContentSaveOutline v-if="!loading" :size="20" />
				</template>
				Opslaan
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

import { Metadata } from '../../entities/index.js'

export default {
	name: 'EditMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		ContentSaveOutline,
	},
	data() {
		return {
			metadata: {
				title: '',
				version: '',
				summary: '',
				description: '',
			},
			loading: false,
			success: null,
			error: false,
			hasUpdated: false,
		}
	},
	computed: {
		inputValidation() {
			const metadataItem = new Metadata({
				...this.metadata,
			})

			const result = metadataItem.validate()

			return {
				success: result.success,
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	mounted() {
		// metadataStore.metadataItem can be false, so only assign metadataStore.metadataItem to metadata if its NOT false
		metadataStore.metaDataItem && (this.metadata = metadataStore.metaDataItem)
	},
	updated() {
		if (navigationStore.modal === 'editMetaData' && this.hasUpdated) {
			if (this.metadata.id === metadataStore.metaDataItem.id) return
			this.hasUpdated = false
		}
		if (navigationStore.modal === 'editMetaData' && !this.hasUpdated) {
			metadataStore.metaDataItem && (this.metadata = metadataStore.metaDataItem)
			this.fetchData(metadataStore.metaDataItem.id)
			this.hasUpdated = true
		}
	},
	methods: {
		fetchData(id) {
			this.loading = true

			metadataStore.getOneMetadata(id)
				.then(() => {
					this.loading = false
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		editMetaData() {
			this.loading = true

			const metadataItem = new Metadata({
				...this.metadata,
			})

			metadataStore.editMetadata(metadataItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					navigationStore.setSelected('metaData')
					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
					}, 2000)
				}).catch((err) => {
					this.error = err
					this.loading = false
				})
		},
	},
}
</script>

<style>
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}

.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.success {
    color: green;
}
</style>
