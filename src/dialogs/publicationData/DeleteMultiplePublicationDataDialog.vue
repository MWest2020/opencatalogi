<template>
	<NcDialog
		name="Eigenschappen verwijderen"
		:can-close="false">
		<div v-if="success !== null || error">
			<NcNoteCard v-if="success" type="success">
				<p>Publicatie eigenschappen succesvol verwijderd</p>
			</NcNoteCard>
			<NcNoteCard v-if="!success" type="error">
				<p>Er is iets fout gegaan bij het verwijderen van publicatie eigenschappen</p>
			</NcNoteCard>
			<NcNoteCard v-if="error" type="error">
				<p>{{ error }}</p>
			</NcNoteCard>
		</div>
		<p v-if="success === null">
			Weet je zeker dat je {{ `${keysToDelete.length === 1 ? '' : 'deze'} ${keysToDelete.length} ${keysToDelete.length === 1 ? 'eigenschap' : 'eigenschappen'}` }} definitief wilt verwijderen?
			Deze actie kan niet ongedaan worden gemaakt.
		</p>
		<ul>
			<li v-for="(key, index) in keysToDelete" :key="index">
				<strong>{{ key }}</strong>
			</li>
		</ul>
		<template #actions>
			<NcButton :disabled="loading" @click="cancel">
				<template #icon>
					<Cancel :size="20" />
				</template>
				{{ success !== null ? 'Sluiten' : 'Annuleer' }}
			</NcButton>
			<NcButton
				v-if="success === null"
				:disabled="loading"
				type="error"
				@click="deleteMultipleProperties">
				<template #icon>
					<NcLoadingIcon v-if="loading" :size="20" />
					<Delete v-if="!loading" :size="20" />
				</template>
				Verwijderen
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
import { NcButton, NcDialog, NcNoteCard, NcLoadingIcon } from '@nextcloud/vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import { Publication } from '../../entities/index.js'
import { navigationStore, publicationStore } from '../../store/store.js'

export default {
	name: 'DeleteMultiplePublicationDataDialog',
	components: { NcDialog, NcButton, NcNoteCard, NcLoadingIcon, Cancel, Delete },
	props: {
		keysToDelete: { type: Array, required: true },
	},
	data() {
		return { loading: false, success: null, error: false }
	},
	methods: {
		deleteMultipleProperties() {
			this.loading = true
			const publicationClone = { ...publicationStore.publicationItem }
			const dataClone = { ...publicationClone.data }
			this.keysToDelete.forEach(key => { delete dataClone[key] })
			const updatedPublication = new Publication({
				...publicationStore.publicationItem,
				data: dataClone,
				catalog: publicationStore.publicationItem.catalog.id ?? publicationStore.publicationItem.catalog,
				publicationType: publicationStore.publicationItem.publicationType,
			})
			publicationStore.editPublication(updatedPublication)
				.then(({ response }) => {
					this.loading = false
					this.success = response.ok
					this.$emit('done', updatedPublication)
					setTimeout(() => {
						this.success = null
						navigationStore.setDialog(false)
					}, 2000)
				})
				.catch(err => {
					this.error = err
					this.loading = false
				})
		},
		cancel() {
			this.$emit('cancel')
			navigationStore.setDialog(false)
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
