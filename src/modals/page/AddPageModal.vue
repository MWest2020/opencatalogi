<script setup>
import { navigationStore, pageStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="navigationStore.modal === 'pageAdd'"
		ref="modalRef"
		label-id="addPageModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Pagina toevoegen</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Pagina succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Pagina</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>
			<div class="formContainer">
				<div v-if="success === null" class="form-group">
					<NcTextField
						:disabled="loading"
						label="Naam"
						:value.sync="page.name"
						:error="!!inputValidation.fieldErrors?.['name']"
						:helper-text="inputValidation.fieldErrors?.['name']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="Slug"
						:value.sync="page.slug"
						:error="!!inputValidation.fieldErrors?.['slug']"
						:helper-text="inputValidation.fieldErrors?.['slug']?.[0]" />
					<NcTextArea
						:disabled="loading"
						label="Inhoud"
						:value.sync="page.contents"
						:error="!!inputValidation.fieldErrors?.['contents']"
						:helper-text="inputValidation.fieldErrors?.['contents']?.[0]" />
				</div>
			</div>
			<NcButton v-if="success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || loading"
				type="primary"
				@click="addPage()">
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
import {
	NcButton,
	NcLoadingIcon,
	NcModal,
	NcNoteCard,
	NcTextArea,
	NcTextField,
} from '@nextcloud/vue'
import Plus from 'vue-material-design-icons/Plus.vue'

import { Page } from '../../entities/index.js'

export default {
	name: 'AddPageModal',
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
			page: {
				name: '',
				slug: '',
				contents: '',
			},

			errorCode: '',
			pageLoading: false,
			hasUpdated: false,
			loading: false,
			success: null,
			error: false,
		}
	},
	computed: {
		inputValidation() {
			const pageItem = new Page({
				...this.page,
			})

			const result = pageItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	updated() {
		if (navigationStore.modal === 'pageAdd' && !this.hasUpdated) {
			this.hasUpdated = true
		}
	},
	methods: {
		isJsonString(str) {
			try {
				JSON.parse(str)
			} catch (e) {
				return false
			}
			return true
		},
		addPage() {
			this.loading = true
			this.error = false

			const pageItem = new Page({
				...this.page,
			})

			pageStore.addPage(pageItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						navigationStore.setModal(false)
						self.success = null
						self.hasUpdated = false
						self.page = {
							name: '',
							slug: '',
							contents: '',
						}
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					self.hasUpdated = false
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
</style>
