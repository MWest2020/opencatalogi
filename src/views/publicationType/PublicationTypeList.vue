<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value="objectStore.getSearchTerm('publicationType')"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="objectStore.getSearchTerm('publicationType') !== ''"
					@update:value="(value) => objectStore.setSearchTerm('publicationType', value)"
					@trailing-button-click="objectStore.clearSearch('publicationType')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton
						title="Bekijk de documentatie over publicatietypes"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/publicatietypes', '_blank')">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
						Help
					</NcActionButton>
					<NcActionButton :disabled="objectStore.isLoading('publicationType')"
						@click="objectStore.fetchCollection('publicationType')">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="navigationStore.setModal('addPublicationType')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Publicatietype toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!objectStore.isLoading('publicationType')">
				<NcListItem v-for="(publicationType, i) in objectStore.getCollection('publicationType').results"
					:key="`${publicationType}${i}`"
					:name="publicationType.title"
					:details="publicationType.description"
					:active="objectStore.getActiveObject('publicationType')?.id === publicationType?.id"
					:force-display-actions="true"
					@click="objectStore.getActiveObject('publicationType')?.id === publicationType?.id ? objectStore.clearActiveObject('publicationType') : objectStore.setActiveObject('publicationType', publicationType)">
					<template #icon>
						<FileTreeOutline :class="objectStore.getActiveObject('publicationType')?.id === publicationType.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #actions>
						<NcActionButton @click="objectStore.setActiveObject('publicationType', publicationType); navigationStore.setModal('editPublicationType')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="objectStore.setActiveObject('publicationType', publicationType); navigationStore.setDialog('copyPublicationType')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton @click="objectStore.setActiveObject('publicationType', publicationType); navigationStore.setDialog('deletePublicationType')">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="objectStore.isLoading('publicationType')"
				:size="64"
				class="loadingIcon"
				appearance="dark"
				name="Publicatietypes aan het laden" />

			<div v-if="!objectStore.getCollection('publicationType').results.length" class="emptyListHeader">
				Er zijn nog geen publicatietypes gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>

<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import FileTreeOutline from 'vue-material-design-icons/FileTreeOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'

export default {
	name: 'PublicationTypeList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon,
		NcActions,
		// Icons
		Magnify,
		FileTreeOutline,
		Plus,
		Pencil,
		Delete,
		Refresh,
		ContentCopy,
		HelpCircleOutline,
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>

<style>
.listHeader {
    position: sticky;
    top: 0;
    z-index: 1000;
    background-color: var(--color-main-background);
    border-bottom: 1px solid var(--color-border);
}

.searchField {
    padding-inline-start: 65px;
    padding-inline-end: 20px;
    margin-block-end: 6px;
}

.selectedZaakIcon>svg {
    fill: white;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
