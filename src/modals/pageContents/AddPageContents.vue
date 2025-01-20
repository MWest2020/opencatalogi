<script setup>
import { pageStore, navigationStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'
</script>

<template>
	<NcModal ref="modalRef"
		label-id="addPageContents"
		@close="closeModal">
		<div class="modal__content">
			<h2>Content toevoegen aan {{ pageStore.pageItem.name }}</h2>
			<div v-if="success !== null || error">
				<NcNoteCard v-if="success" type="success">
					<p>Content succesvol toegevoegd</p>
				</NcNoteCard>
				<NcNoteCard v-if="!success" type="error">
					<p>Er is iets fout gegaan bij het toevoegen van Content</p>
				</NcNoteCard>
				<NcNoteCard v-if="error" type="error">
					<p>{{ error }}</p>
				</NcNoteCard>
			</div>

			<div v-if="success === null" class="form-group">
				<p>
					De volgorde waarin je contents toevoegt maakt uit, let goed op de volgorde.
				</p>

				<NcSelect v-bind="typeOptions"
					v-model="contentsItem.type"
					input-label="Content type"
					required />

				<!-- Richtext -->
				<NcTextArea v-if="contentsItem.type === 'Richtext'"
					:value.sync="contentsItem.richTextData"
					label="Richtext"
					required />

				<!-- Faq -->
				<div v-if="contentsItem.type === 'Faq'">
					<VueDraggable v-model="contentsItem.faqData">
						<div v-for="item in contentsItem.faqData" :key="item.id">
							<div class="draggable-form-item">
								<Drag :size="40" />
								<NcTextField label="Vraag" :value.sync="item.question" />
								<NcTextField label="Antwoord" :value.sync="item.answer" />
							</div>
						</div>
					</VueDraggable>

					<div class="draggable-form-item outside-item">
						<!-- "Fake" drag handle, or could be hidden -->
						<div class="disabled-drag-handle">
							<Drag :size="40" />
						</div>

						<NcTextField v-model="contentsItem.faqNewItem.question" label="Vraag" @blur="addNewFaqItem" />
						<NcTextField v-model="contentsItem.faqNewItem.answer" label="Antwoord" @blur="addNewFaqItem" />
					</div>
				</div>
			</div>

			<NcButton v-if="success === null"
				:disabled="!contentsItem.type || loading"
				type="primary"
				@click="addCatalogPublicationType">
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
import { NcButton, NcModal, NcLoadingIcon, NcNoteCard, NcSelect, NcTextArea } from '@nextcloud/vue'
import { VueDraggable } from 'vue-draggable-plus'

import Plus from 'vue-material-design-icons/Plus.vue'
import Drag from 'vue-material-design-icons/Drag.vue'

import { Page } from '../../entities/index.js'

export default {
	name: 'AddPageContents',
	components: {
		NcModal,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcSelect,
		NcTextArea,
		VueDraggable,
		// Icons
		Plus,
	},
	data() {
		return {
			contentsItem: {
				type: '',
				richTextData: '',
				faqData: [
					{
						id: crypto.randomUUID?.() || String(Date.now()) + Math.random(),
						question: '',
						answer: '',
					},
				],
				// fake FAQ item used to add new FAQ items
				faqNewItem: {
					id: crypto.randomUUID?.() || String(Date.now()) + Math.random(),
					question: '',
					answer: '',
				},
			},
			typeOptions: {
				options: ['Richtext', 'Faq'],
			},
			loading: false,
			success: null,
			error: false,
			errorCode: '',
			hasUpdated: false,
		}
	},
	watch: {
		'contentsItem.faqData': {
			handler(newVal) {
				const currentFaqLength = newVal.length

				// check if last FAQ is empty, if so remove it
				if (newVal[currentFaqLength - 1].question === '' && newVal[currentFaqLength - 1].answer === '') {
					newVal.pop()
				}
			},
			deep: true,
		},
	},
	methods: {
		addNewFaqItem() {
			console.log('GOT HERE')
			const newFaqItem = this.contentsItem.faqNewItem
			const hasData = !!(newFaqItem.question || newFaqItem.answer)

			if (hasData) {
				this.contentsItem.faqData.push({ ...newFaqItem })

				this.contentsItem.faqNewItem = {
					id: crypto.randomUUID?.() || String(Date.now()) + Math.random(),
					question: '',
					answer: '',
				}
			}
		},
		closeModal() {
			navigationStore.setModal(false)
			this.catalogiItem = {
				title: '',
				summary: '',
				description: '',
				listed: false,
				publicationTypes: [],
			}
		},
		addCatalogPublicationType() {
			this.loading = true
			this.error = false

			this.catalogiItem.publicationTypes.push(this.publicationTypes.value.id)

			const newPageItem = new Page({
				...pageStore.pageItem,
			})

			pageStore.editPage(newPageItem)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok

					// Wait for the user to read the feedback then close the model
					const self = this
					setTimeout(function() {
						self.success = null
						self.closeModal()
					}, 2000)

					this.hasUpdated = false
				})
				.catch((err) => {
					this.error = err
					this.loading = false
					this.hasUpdated = false
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

<style scoped>
.draggable-form-item {
    display: flex;
    align-items: center;
    gap: 3px;

    background-color: rgba(255, 255, 255, 0.05);
    padding: 4px;
    border-radius: 4px;

    margin-block: 8px;
}
.draggable-form-item :deep(.v-select) {
    min-width: 150px;
}

.disabled-drag-handle {
    cursor: not-allowed;
}
</style>
