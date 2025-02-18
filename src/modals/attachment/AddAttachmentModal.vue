<script setup>
import { navigationStore, publicationStore } from '../../store/store.js'
</script>

<template>
	<NcModal v-if="navigationStore.modal === 'AddAttachment'"
		ref="modalRef"
		label-id="AddAttachmentModal"
		@close="navigationStore.setModal(false)">
		<div class="modal__content TestMappingMainModal">
			<h2>Bijlage toevoegen</h2>

			<div>
				<NcSelect v-bind="labelOptions"
					v-model="labelOptions.value"
					:taggable="true"
					:multiple="true"
					:selectable="(option) => isSelectable(option)" />
			</div>

			<div class="container">
				<div v-if="!labelOptions.value?.length" class="filesListDragDropNotice" :class="'tabPanelFileUpload'">
					<div v-if="!labelOptions.value?.length">
						<NcNoteCard type="info">
							<p>Please select or create labels or select "Geen label" to add files</p>
						</NcNoteCard>
					</div>
					<div v-if="success !== null || error">
						<NcNoteCard v-if="success" type="success">
							<p>Successfully imported files</p>
						</NcNoteCard>
						<NcNoteCard v-if="error && !success" type="error">
							<p>Something went wrong while importing</p>
						</NcNoteCard>
						<NcNoteCard v-if="error && !success" type="error">
							<p>{{ error }}</p>
						</NcNoteCard>
						<div v-if="false">
							<NcNoteCard type="error">
								<p>Please select files with the correct extension</p>
							</NcNoteCard>
						</div>
					</div>
					<div class="filesListDragDropNoticeWrapper" :class="{ 'filesListDragDropNoticeWrapper--disabled': !labelOptions.value?.length }">
						<div class="filesListDragDropNoticeWrapperIcon">
							<TrayArrowDown :size="48" />
							<h3 class="filesListDragDropNoticeTitle">
								Drag and drop a file or files here
							</h3>
						</div>

						<h3 class="filesListDragDropNoticeTitle">
							Or
						</h3>

						<div class="filesListDragDropNoticeTitle">
							<NcButton
								:disabled="loading || !labelOptions.value?.length"
								type="primary"
								@click="openFileUpload()">
								<template #icon>
									<Plus :size="20" />
								</template>
								Add a file or files
							</NcButton>
						</div>
					</div>
				</div>
				<div v-if="labelOptions.value?.length"
					ref="dropZoneRef"
					class="filesListDragDropNotice"
					:class="'tabPanelFileUpload'">
					<div v-if="!labelOptions.value?.length">
						<NcNoteCard type="info">
							<p>Please select or create labels or select "Geen label" to add files</p>
						</NcNoteCard>
					</div>
					<div v-if="success !== null || error">
						<NcNoteCard v-if="success" type="success">
							<p>Successfully imported files</p>
						</NcNoteCard>
						<NcNoteCard v-if="error && !success" type="error">
							<p>Something went wrong while importing</p>
						</NcNoteCard>
						<NcNoteCard v-if="error && !success" type="error">
							<p>{{ error }}</p>
						</NcNoteCard>
						<div v-if="false">
							<NcNoteCard type="error">
								<p>Please select files with the correct extension</p>
							</NcNoteCard>
						</div>
					</div>
					<div class="filesListDragDropNoticeWrapper" :class="{ 'filesListDragDropNoticeWrapper--disabled': !labelOptions.value?.length }">
						<div class="filesListDragDropNoticeWrapperIcon">
							<TrayArrowDown :size="48" />
							<h3 class="filesListDragDropNoticeTitle">
								Drag and drop a file or files here
							</h3>
						</div>

						<h3 class="filesListDragDropNoticeTitle">
							Or
						</h3>

						<div class="filesListDragDropNoticeTitle">
							<NcButton
								:disabled="loading || !labelOptions.value?.length"
								type="primary"
								@click="openFileUpload()">
								<template #icon>
									<Plus :size="20" />
								</template>
								Add a file or files
							</NcButton>
						</div>
					</div>
				</div>
				<div v-if="!files">
					No files selected
				</div>
				<div v-if="files" class="importButtonContainer">
					<NcButton
						:disabled="loading || checkForTooBigFiles(files)"
						type="primary"
						@click="importFiles()">
						<template #icon>
							<NcLoadingIcon v-if="loading" :size="20" />
							<FileImportOutline v-if="!loading" :size="20" />
						</template>
						Import
					</NcButton>
				</div>
				<table v-if="files" class="files-table">
					<thead>
						<tr class="files-table-tr">
							<th>
								Name
							</th>
							<th>
								Size
							</th>
							<th>
								Labels
							</th>
							<th />
						</tr>
					</thead>
					<tbody>
						<tr v-for="file of files" :key="file.name" class="files-table-tr">
							<td class="files-table-td-name" :class="{ 'files-table-name-wrong': getTooBigFiles(file.size) }">
								<span class="files-table-name">{{ getFileNameAndExtension(file.name).name }}</span>
								<span class="files-table-extension">.{{ getFileNameAndExtension(file.name).extension }}</span>
							</td>
							<td>
								{{ bytesToSize(file.size) }}
							</td>
							<td class="files-table-td-labels">
								<span v-if="editingTags !== file.name"
									class="files-list__row-action--inline files-list__row-action-system-tags">
									<ul v-if="file.tags && file.tags.length > 0" class="files-list__system-tags" aria-label="Assigned collaborative tags">
										<li v-for="label of file.tags"
											:key="label"
											class="files-list__system-tag"
											:title="label">
											{{ label }}
										</li>
									</ul>
									<span v-if="!file.tags || file.tags.length === 0">
										Geen labels
									</span>
								</span>
								<NcSelect
									v-if="editingTags === file.name"
									v-model="file.tags"
									:taggable="true"
									:multiple="true"
									:options="labelOptionsEdit.options" />

								<NcButton
									v-if="editingTags !== file.name"
									:disabled="editingTags && editingTags !== file.name"
									type="secondary"
									class="editTagsButton"
									@click="editingTags = file.name">
									<template #icon>
										<TagEditIcon :size="20" />
									</template>
								</NcButton>
								<NcButton
									v-if="editingTags === file.name"
									type="primary"
									class="editTagsButton"
									@click="saveTags(files)">
									<template #icon>
										<ContentSaveOutline :size="20" />
									</template>
								</NcButton>
							</td>
							<td class="files-table-remove-button">
								<NcButton
									:disabled=" loading"
									type="primary"
									@click="reset(file.name)">
									<template #icon>
										<Minus :size="20" />
									</template>
									<span>remove</span>
								</NcButton>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</NcModal>
</template>

<script>
import { NcButton, NcLoadingIcon, NcModal, NcNoteCard, NcTextArea, NcTextField, NcSelect } from '@nextcloud/vue'
import { useFileSelection } from './../../composables/UseFileSelection.js'

import { ref } from 'vue'

import Minus from 'vue-material-design-icons/Minus.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import TrayArrowDown from 'vue-material-design-icons/TrayArrowDown.vue'
import TagEditIcon from 'vue-material-design-icons/TagEdit.vue'
import FileImportOutline from 'vue-material-design-icons/FileImportOutline.vue'
import ContentSaveOutline from 'vue-material-design-icons/ContentSaveOutline.vue'

import axios from 'axios'

import { Publication, Attachment } from '../../entities/index.js'

const dropZoneRef = ref()

console.log({ dropZoneRef })
const { openFileUpload, files, reset, setFiles, setTags } = useFileSelection({ allowMultiple: true, dropzone: dropZoneRef })

export default {
	name: 'AddAttachmentModal',
	components: {
		NcModal,
		NcTextField,
		NcTextArea,
		NcButton,
		NcLoadingIcon,
		NcNoteCard,
		NcSelect,
	},
	props: {
		dropFiles: {
			type: Array,
			required: false,
			default: null,
		},
	},
	data() {
		return {
			loading: false,
			success: null,
			error: false,
			editingTags: null,
			labelOptions: {
				inputLabel: 'Labels',
				multiple: true,
				options: ['Geen label', 'Besluit', 'Convenant', 'Document', 'Informatieverzoek', 'Inventarisatielijst'],
			},
			labelOptionsEdit: {
				inputLabel: 'Labels',
				multiple: true,
				options: ['Besluit', 'Convenant', 'Document', 'Informatieverzoek', 'Inventarisatielijst'],
			},
		}
	},
	computed: {
		inputValidation() {
			const catalogiItem = new Attachment({
				...publicationStore.attachmentItem,
			})

			const result = catalogiItem.validate()

			return {
				success: result.success,
				errorMessages: result?.error?.issues.map((issue) => `${issue.path.join('.')}: ${issue.message}`) || [],
				fieldErrors: result?.error?.formErrors?.fieldErrors || {},
			}
		},
	},
	watch: {
		dropFiles: {
			handler(addedFiles) {
				// publicationStore.attachmentFile && setFiles(addedFiles)
			},
			deep: true,
		},
		labelOptions: {
			handler() {
				setTags(this.getLabels())
			},
			deep: true,
		},
	},
	mounted() {
		publicationStore.setAttachmentItem([])
	},
	methods: {
		bytesToSize(bytes) {
			const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB']
			if (bytes === 0) return 'n/a'
			const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)))
			if (i === 0 && sizes[i] === 'Bytes') return '< 1 KB'
			if (i === 0) return bytes + ' ' + sizes[i]
			return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i]
		},

		getFileNameAndExtension(fullname) {
			const lastDot = fullname.lastIndexOf('.')
			const name = fullname.slice(0, lastDot)
			const extension = fullname.slice(lastDot + 1)
			return { name, extension }
		},

		checkForTooBigFiles(files) {
			if (!files) return false
			const wrongFiles = files.filter(file => {
				return this.getTooBigFiles(file.size)
			})

			return wrongFiles.length > 0
		},

		getTooBigFiles(size) {
			return size > 100000000 // 100MB
		},

		isSelectable(option) {
			if (this.labelOptions.value?.includes('Geen label') && option !== 'Geen label') {
				return false
			}
			if (this.labelOptions.value?.length >= 1 && !this.labelOptions.value?.includes('Geen label') && option === 'Geen label') {
				return false
			}
			return true
		},

		getLabels() {
			if (this.labelOptions.value?.includes('Geen label')) {
				return null
			} else {
				return this.labelOptions.value
			}
		},

		saveTags(files) {
			this.editingTags = null
			console.log(files)
		},

		closeModal() {
			navigationStore.modal = false
		},
		checkIfDisabled() {
			if (publicationStore.attachmentItem.downloadUrl || publicationStore.attachmentItem.title) return true
			return false
		},

		importFiles() {
			console.log(files)
			this.success = true
			setTimeout(() => {
				this.success = null
			}, 10000)
		},
		// addAttachment() {
		// 	this.loading = true
		// 	this.errorMessage = false

		// 	axios.post('/index.php/apps/opencatalogi/api/attachments', {
		// 		...(publicationStore.attachmentItem),
		// 		published: null,
		// 		_file: files.value ? files.value[0] : '',
		// 	}, {
		// 		headers: {
		// 			'Content-Type': 'multipart/form-data',
		// 			// These headers are used to pass along some publication info to use as name for a Folder,
		// 			// to store (attachments/) files in for that specific publication,
		// 			'Publication-Id': publicationStore.publicationItem.id,
		// 			'Publication-Title': publicationStore.publicationItem.title,
		// 		},
		// 	}).then((response) => {

		// 		this.success = true
		// 		reset()

		// 		// Let's refresh the attachment list
		// 		if (publicationStore.publicationItem) {
		// 			publicationStore.getPublicationAttachments(publicationStore.publicationItem?.id)

		// 			const newPublicationItem = new Publication({
		// 				...publicationStore.publicationItem,
		// 				attachments: [...publicationStore.publicationItem.attachments, response.data.id],
		// 				catalog: publicationStore.publicationItem.catalog.id ?? publicationStore.publicationItem.catalog,
		// 				publicationType: publicationStore.publicationItem.publicationType,
		// 			})

		// 			publicationStore.editPublication(newPublicationItem)
		// 				.then(() => {
		// 					this.loading = false
		// 				})
		// 				.catch((err) => {
		// 					this.error = err
		// 					this.loading = false
		// 				})
		// 		// store.refreshCatalogiList()
		// 		}
		// 		// publicationStore.setAttachmentItem(response)

		// 		// Wait for the user to read the feedback then close the model
		// 		const self = this
		// 		setTimeout(function() {
		// 			self.success = null
		// 			navigationStore.setModal(false)
		// 		}, 2000)
		// 	})
		// 		.catch((err) => {
		// 			this.error = err.response?.data?.error ?? err
		// 			this.loading = false
		// 		})
		// },
	},
}
</script>

<style>
div[class='modal-container']:has(.TestMappingMainModal) {
    width: clamp(1000px, 100%, 1200px) !important;
}
.modal__content {
    margin: var(--OC-margin-50);
    text-align: center;
}
</style>

<style scoped>
.zaakDetailsContainer {
    margin-block-start: var(--OC-margin-20);
    margin-inline-start: var(--OC-margin-20);
    margin-inline-end: var(--OC-margin-20);
}

.filesListDragDropNoticeWrapper--disabled{
	opacity: 0.4;
}

.success {
    color: green;
}

.importButtonContainer {
	display: flex;
	justify-content: flex-end;
}

.container {
	padding-inline: 25px;
}

.files-table-name-wrong > span {
	color: #ff0000 !important;
}

.files-table {
	width: 100%;
	border-collapse: collapse;
}

.files-table-td-name{
	overflow: hidden;
	white-space: nowrap;
	text-overflow: ellipsis;
	max-width: 75ch;
}

.files-table-td-name span {
  float: left;
  overflow: hidden;
  white-space: nowrap;
  text-overflow: ellipsis;
  max-width: calc(100% - 15%);
}

.files-table-name {
  color: var(--color-main-text);
}
.files-table-extension {
  color: var(--color-text-maxcontrast);
}

.files-table-tr {
  color: var(--color-text-maxcontrast);
  border-bottom: 1px solid var(--color-border);
}

.files-table-tr:hover {
    background-color: var(--color-background-hover);
    --color-text-maxcontrast: var(--color-main-text);
	--color-border: var(--color-border-dark);
}

.files-table-tr > td {
  height: 55px;
}

.files-table-remove-button {
  text-align: -webkit-right;
}

.files-list__row-icon {
  position: relative;
  display: flex;
  overflow: visible;
  align-items: center;
  flex: 0 0 32px;
  justify-content: center;
  width: 32px;
  height: 100%;
  margin-right: var(--checkbox-padding);
  color: var(--color-primary-element);
}

.files-list__row-action-system-tags {
  margin-right: 7px;
  display: flex;
}

.files-list__system-tags {
	--min-size: 32px;
	display: flex;
	justify-content: center;
	align-items: center;
	min-width: calc(var(--min-size)* 2);
	max-width: 300px;
}

.files-list__system-tag {
	padding: 5px 10px;
	border: 1px solid;
	border-radius: var(--border-radius-pill);
	border-color: var(--color-border);
	color: var(--color-text-maxcontrast);
	height: var(--min-size);
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	line-height: 22px;
	text-align: center;
	box-sizing: border-box;
}

.files-list__system-tag:not(:first-child) {
	margin-inline-start: 5px;
}

.editTagsButton {
	margin-inline-end: 3px;
}

.files-table-td-labels {
	display: flex;
	justify-content: space-between;
	text-align: unset;
	align-items: center;
	-webkit-box-align: end;
	box-sizing: border-box;
	min-width: 410px;
}
</style>
