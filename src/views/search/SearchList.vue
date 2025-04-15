<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value="objectStore.getSearchTerm('publication')"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="objectStore.getSearchTerm('publication') !== ''"
					@update:value="(value) => objectStore.setSearchTerm('publication', value)"
					@trailing-button-click="objectStore.clearSearch('publication')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActionButton class="refresh" @click="objectStore.fetchCollection('publication')">
					<template #icon>
						<Refresh :size="20" />
					</template>
				</NcActionButton>
			</div>
			<div v-if="!objectStore.isLoading('publication')">
				<NcListItem v-for="(publication, i) in objectStore.getCollection('publication').results"
					:key="`${publication}${i}`"
					:name="publication.name"
					:bold="false"
					:force-display-actions="true"
					:active="objectStore.getActiveObject('publication')?.id === publication.id"
					:details="publication?.status"
					@click="objectStore.setActiveObject('publication', publication)">
					<template #icon>
						<FileDocumentOutline :size="44" />
					</template>
					<template #subname>
						{{ publication?.description }}
					</template>
					<template #actions>
						<NcActionButton @click="objectStore.setActiveObject('publication', publication); navigationStore.setModal('publication')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="objectStore.setActiveObject('publication', publication); navigationStore.setDialog('copyObject', { objectType: 'publication', dialogName: 'copyObject', displayName: 'Publicatie' })">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton @click="objectStore.setActiveObject('publication', publication); navigationStore.setDialog('deleteCatalog', { objectType: 'publication', dialogName: 'deleteCatalog', displayName: 'Publicatie' })">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="objectStore.isLoading('publication')"
				:size="64"
				class="loadingIcon"
				appearance="dark"
				name="Publicaties aan het laden" />

			<div v-if="!objectStore.getCollection('publication').results.length" class="emptyListHeader">
				Er zijn nog geen publicaties gevonden.
			</div>
		</ul>
	</NcAppContentList>
</template>

<script>
import { NcActionButton, NcAppContentList, NcListItem, NcLoadingIcon, NcTextField } from '@nextcloud/vue'

// Icons
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import FileDocumentOutline from 'vue-material-design-icons/FileDocumentOutline.vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'

export default {
	name: 'SearchList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon,
		// Icons
		Magnify,
		Refresh,
		ContentCopy,
		FileDocumentOutline,
		Pencil,
		Delete,
	},
}
</script>

<style>
.listHeader{
	display: flex;
}

.refresh{
	margin-block-start: 11px !important;
    margin-block-end: 11px !important;
    margin-inline-end: 10px;
}

.active.publicationDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.publicationDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
