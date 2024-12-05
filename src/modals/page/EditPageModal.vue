<script setup>
import { navigationStore, pageStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'
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
					<div :class="`codeMirrorContainer ${getTheme()}`">
						<CodeMirror
							v-model="pageItem.contents"
							:basic="true"
							placeholder="{ &quot;key&quot;: &quot;value&quot; }"
							:dark="getTheme() === 'dark'"
							:gutter="true"
							:linter="jsonParseLinter()"
							:lang="json()" />
						<NcButton class="prettifyButton" :disabled="!pageItem.contents || !verifyJsonValidity(pageItem.contents)" @click="prettifyJson">
							<template #icon>
								<AutoFix :size="20" />
							</template>
							Prettify
						</NcButton>
					</div>
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
	NcTextField,
} from '@nextcloud/vue'
import CodeMirror from 'vue-codemirror6'
import { json, jsonParseLinter } from '@codemirror/lang-json'

import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'
import AutoFix from 'vue-material-design-icons/AutoFix.vue'

import { Page } from '../../entities/index.js'

export default {
	name: 'EditPageModal',
	components: {
		NcModal,
		NcTextField,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		CodeMirror,
		// Icons
		ContentSaveOutline,
		AutoFix,
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
					contents: JSON.stringify(JSON.parse(pageStore.pageItem.contents), null, 2) || '',
				}
			}
		},
		prettifyJson() {
			this.pageItem.contents = JSON.stringify(JSON.parse(this.pageItem.contents), null, 2)
		},
		editPage() {
			this.loading = true
			this.error = false
			const pageItem = new Page({
				...this.pageItem,
				contents: JSON.stringify(JSON.parse(this.pageItem.contents), null, 2),
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
.success {
  color: green;
}
</style>
