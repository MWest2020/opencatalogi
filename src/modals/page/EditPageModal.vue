<script setup>
import { navigationStore, pageStore } from '../../store/store.js'
</script>
<template>
	<NcModal
		v-if="navigationStore.modal === 'editPage'"
		ref="modalRef"
		label-id="editPageModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content">
			<h2>Pagina bewerken</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Pagina succesvol bewerkt</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het bewerken van Pagina</p>
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
						:value.sync="pageItem.name"
						:error="!!inputValidation.fieldErrors?.['name']"
						:helper-text="inputValidation.fieldErrors?.['name']?.[0]" />
					<NcTextField
						:disabled="loading"
						label="Slug"
						:value.sync="pageItem.slug"
						:error="!!inputValidation.fieldErrors?.['slug']"
						:helper-text="inputValidation.fieldErrors?.['slug']?.[0]" />
					<NcTextArea
						:disabled="loading"
						label="Inhoud"
						placeholder="{ &quot;key&quot;: &quot;value&quot; }"
						:value.sync="pageItem.contents"
						:error="!verifyJsonValidity(pageItem.contents)"
						:helper-text="!verifyJsonValidity(pageItem.contents) ? 'This is not valid JSON (optional)' : ''" />
				</div>
			</div>
			<NcButton v-if="success === null"
				v-tooltip="inputValidation.errorMessages?.[0]"
				:disabled="!inputValidation.success || loading || !verifyJsonValidity(pageItem.contents)"
				type="primary"
				@click="editPage()">
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
import {
	NcButton,
	NcLoadingIcon,
	NcModal,
	NcNoteCard,
	NcTextArea,
	NcTextField,
} from '@nextcloud/vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

import { Page } from '../../entities/index.js'

export default {
	name: 'EditPageModal',
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
			pageItem: {
				name: '',
				slug: '',
				contents: '',
			},
			loading: false,
			success: null,
			error: false,
			hasUpdated: false,
		}
	},
	computed: {
		inputValidation() {
			const pageItem = new Page({
				...this.pageItem,
			})

			const result = pageItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	mounted() {
		// pageStore.pageItem can be false, so only assign pageStore.pageItem to pageItem if its NOT false
		pageStore.pageItem && (this.pageItem = pageStore.pageItem)
		this.initializePageItem()
	},
	updated() {
		if (navigationStore.modal === 'editPage' && this.hasUpdated) {
			if (this.pageItem.id === pageStore.pageItem.id) return
			this.hasUpdated = false
		}
		if (navigationStore.modal === 'editPage' && !this.hasUpdated) {
			pageStore.pageItem && (this.pageItem = pageStore.pageItem)
			this.hasUpdated = true
		}
		this.initializePageItem()
	},
	methods: {
		initializePageItem() {
			if (pageStore.pageItem?.id) {
				this.pageItem = {
					...pageStore.pageItem,
					contents: pageStore.pageItem.contents || '{}'
				}
			}
		},
		editPage() {
			this.loading = true
			this.error = false

			const pageItem = new Page({
				...this.pageItem,
			})

			pageStore.editPage(pageItem)
				.then(({ response }) => {

					this.loading = false
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						navigationStore.setModal(false)
					}, 2000)
				})
				.catch((err) => {
					this.error = err
					this.loading = false
				})
		},
		verifyJsonValidity(jsonInput) {
			if (jsonInput === '') return true
			try {
				JSON.parse(jsonInput)
				return true
			} catch (e) {
				return false
			}
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
