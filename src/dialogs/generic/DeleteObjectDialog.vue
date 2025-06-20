<script setup>
import { navigationStore, objectStore, catalogStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		v-if="shouldShowDialog"
		:name="`${dialogTitle}${isMultiple ? 's' : ''} verwijderen`"
		:can-close="false">
		<div v-if="objectStore.getState(objectType).success !== null || objectStore.getState(objectType).error">
			<NcNoteCard v-if="objectStore.getState(objectType).success" type="success">
				<p>{{ dialogTitle }}{{ isMultiple ? 's' : '' }} succesvol verwijderd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!objectStore.getState(objectType).success && objectStore.getState(objectType).error !== 'Invalid configuration for object type: publication'" type="error">
				<p>Er is iets fout gegaan bij het verwijderen van {{ dialogTitle.toLowerCase() }}{{ isMultiple ? 's' : '' }}</p>
			</NcNoteCard>
			<NcNoteCard v-if="objectStore.getState(objectType).error && objectStore.getState(objectType).error !== 'Invalid configuration for object type: publication'" type="error">
				<p>{{ objectStore.getState(objectType).error }}</p>
			</NcNoteCard>
		</div>
		<div v-if="objectStore.isLoading(objectType)" class="loading-status">
			<NcLoadingIcon :size="20" />
			<span>{{ dialogTitle }}{{ isMultiple ? 's' : '' }} {{ isMultiple ? 'worden' : 'wordt' }} verwijderd...</span>
		</div>
		<p v-if="objectStore.getState(objectType).success === null && !objectStore.isLoading(objectType)">
			<template v-if="isMultiple">
				Wil je de geselecteerde {{ dialogTitle.toLowerCase() }}s definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
			</template>
			<template v-else>
				Wil je <b>{{ objectStore.getActiveObject(objectType)?.name || objectStore.getActiveObject(objectType)?.title }}</b> definitief verwijderen? Deze actie kan niet ongedaan worden gemaakt.
			</template>
		</p>
		<template v-if="objectStore.getState(objectType).success === null && !objectStore.isLoading(objectType)" #actions>
			<NcButton
				:disabled="objectStore.isLoading(objectType)"
				icon=""
				@click="navigationStore.setDialog(false)">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Annuleer
			</NcButton>
			<NcButton
				:disabled="objectStore.isLoading(objectType)"
				icon="Delete"
				type="error"
				@click="deleteObject">
				<template #icon>
					<Delete :size="20" />
				</template>
				Verwijderen
			</NcButton>
		</template>
		<template v-else #actions>
			<NcButton
				icon=""
				@click="closeDialog()">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Sluiten
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import Delete from 'vue-material-design-icons/Delete.vue'

export default {
	name: 'DeleteObjectDialog',
	components: {
		NcDialog,
		NcButton,
		NcNoteCard,
		NcLoadingIcon,
		// Icons
		Cancel,
		Delete,
	},
	data() {
		return {
			closeTimeout: null,
			objectType: null,
		}
	},
	computed: {
		dialogProperties() {
			return navigationStore.dialogProperties
		},
		dialogTitle() {
			return this.dialogProperties?.dialogTitle
		},
		isMultiple() {
			return this.dialogProperties?.isMultiple ?? false
		},
		shouldShowDialog() {
			return navigationStore.dialog === 'deleteObject'
		},
	},
	watch: {
		dialogProperties: {
			immediate: true,
			handler(newProps) {
				this.objectType = newProps?.objectType
			},
		},
	},
	methods: {
		deleteObject() {
			if (this.isMultiple) {
				const selectedObjects = objectStore.getSelectedObjects(this.objectType)
				if (!selectedObjects?.length) return

				const deletePromises = selectedObjects.map(obj =>
					objectStore.deleteObject(this.objectType, obj.id)
						.catch(err => {
							if (
								this.objectType === 'publication'
              && typeof err.message === 'string'
              && err.message.includes('Invalid configuration for object type: publication')
							) {
								objectStore.setState(this.objectType, { success: true, error: null })
								return {}
							}
							return Promise.reject(err)
						}),
				)

				Promise.all(deletePromises)
					.then(responses => {
						this.closeTimeout = setTimeout(() => {
							this.closeDialog()
						}, 2000)
					})
					.catch(err => {
						console.error('Error deleting multiple objects:', err)
					})
					.finally(() => {
						this.refreshObjectList(this.objectType)
					})

			} else {
				const activeObject = objectStore.getActiveObject(this.dialogProperties.objectType)
				if (!activeObject?.id) return

				const publicationData = this.objectType === 'publication'
					? {
						schema: activeObject.schema,
						register: activeObject.register,
					}
					: null

				objectStore.deleteObject(this.dialogProperties.objectType, activeObject.id, publicationData)
					.catch(err => {
						if (
							this.objectType === 'publication'
            && typeof err.message === 'string'
            && err.message.includes('Invalid configuration for object type: publication')
						) {
							objectStore.setState(this.objectType, { success: true, error: null })
							return {}
						}
						return Promise.reject(err)
					})
					.then(response => {
						this.closeTimeout = setTimeout(() => {
							this.closeDialog()
						}, 2000)
					})
					.catch(err => {
						console.error('Error deleting one object:', err)
					})
					.finally(() => {
						this.refreshObjectList(this.objectType)
					})
			}
		},
		refreshObjectList(objectType) {
			switch (objectType) {
			case 'publication':
				catalogStore.fetchPublications()
				break
			}
		},
		closeDialog() {
			if (this.closeTimeout) {
				clearTimeout(this.closeTimeout)
				this.closeTimeout = null
			}

			navigationStore.setDialog(false)
			objectStore.setState(this.objectType, { success: null, error: null })
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

.loading-status {
	display: flex;
	align-items: center;
	justify-content: center;
	gap: 0.5rem;
	margin: 1rem 0;
	color: var(--color-text-lighter);
}
</style>
