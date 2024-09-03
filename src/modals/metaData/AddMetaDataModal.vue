<script setup>
import { navigationStore, metadataStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'addMetaData'"
		ref="modalRef"
		label-id="addMetaDataModal"
		@close="closeModal">
		<div class="modal__content">
			<h2>Publicatie type toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Publicatie type succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van publicatie type</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div v-if="success === null" class="form-group">
				<NcTextField
					label="Titel"
					:value.sync="metadata.title"
					:error="!!inputValidation.fieldErrors?.['title']"
					:helper-text="inputValidation.fieldErrors?.['title']?.[0]" />
				<NcTextField
					label="Versie"
					:value.sync="metadata.version"
					:error="!!inputValidation.fieldErrors?.['version']"
					:helper-text="inputValidation.fieldErrors?.['version']?.[0]" />
				<NcTextField :disabled="loading"
					label="Samenvatting*"
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
			<NcButton v-if="success === null"
				:disabled="!inputValidation.success || loading"
				type="primary"
				@click="addMetaData">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Plus v-if="!loading" :size="20" />
				</template>
				Toevoegen
			</NcButton>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcModal, NcTextField, NcTextArea, NcLoadingIcon, NcNoteCard } from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'

import { Metadata } from '../../entities/index.js'

export default {
	name: 'AddMetaDataModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		// Icons
		Plus,
	},
	data() {
		return {
			metadata: {
				title: '',
				version: '',
				description: '',
				summary: '',
				required: '',
			},
			loading: false,
			success: null,
			error: false,
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
	methods: {
		closeModal() {
			this.success = null
			this.metadata = {
				title: '',
				version: '',
				description: '',
				summary: '',
				required: '',
			}
			navigationStore.setModal(false)
		},
		addMetaData() {
			this.loading = true

			const metadataItem = new Metadata({
				...this.metadata,
			})

			metadataStore.addMetadata(metadataItem)
				.then(({ response }) => {
					// Set the form
					this.loading = false
					this.success = response.ok

					navigationStore.setSelected('metaData')
					// Update the list
					const self = this
					setTimeout(function() {
						self.closeModal()
					}, 2000)
				})
				.catch((err) => {
					this.metaDataLoading = false
					this.error = err
					console.error(err)
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
