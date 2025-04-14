<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value="objectStore.getSearchTerm('catalog')"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="objectStore.getSearchTerm('catalog') !== ''"
					@update:value="(value) => objectStore.setSearchTerm('catalog', value)"
					@trailing-button-click="objectStore.clearSearch('catalog')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActions>
					<NcActionButton
						title="Bekijk de documentatie over catalogi"
						@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/catalogi', '_blank')">
						<template #icon>
							<HelpCircleOutline :size="20" />
						</template>
						Help
					</NcActionButton>
					<NcActionButton :disabled="objectStore.isLoading('catalog')"
						@click="objectStore.fetchCollection('catalog')">
						<template #icon>
							<Refresh :size="20" />
						</template>
						Ververs
					</NcActionButton>
					<NcActionButton @click="objectStore.clearActiveObject('catalog'); navigationStore.setModal('catalog')">
						<template #icon>
							<Plus :size="20" />
						</template>
						Catalog toevoegen
					</NcActionButton>
				</NcActions>
			</div>

			<div v-if="!objectStore.isLoading('catalog')">
				<NcListItem v-for="(catalog, i) in objectStore.getCollection('catalog').results"
					:key="`${catalog}${i}`"
					:name="catalog.title"
					:details="catalog.listed ? 'Publiek vindbaar' : 'Niet publiek vindbaar'"
					:active="objectStore.getActiveObject('catalog')?.id === catalog?.id"
					:counter-number="catalog.publicationTypes.length || '0'"
					:force-display-actions="true"
					@click="objectStore.getActiveObject('catalog')?.id === catalog?.id ? objectStore.clearActiveObject('catalog') : objectStore.setActiveObject('catalog', catalog)">
					<template #icon>
						<DatabaseOutline :class="objectStore.getActiveObject('catalog')?.id === catalog.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #subname>
						{{ catalog?.summary }}
					</template>
					<template #actions>
						<NcActionButton @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setModal('catalog')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="navigationStore.setSelected('publication'); navigationStore.setSelectedCatalog(catalog?.id)">
							<template #icon>
								<OpenInApp :size="20" />
							</template>
							Catalog bekijken
						</NcActionButton>
						<NcActionButton @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setModal('addCatalogiPublicationType')">
							<template #icon>
								<Plus :size="20" />
							</template>
							Publicatietype toevoegen
						</NcActionButton>
						<NcActionButton @click="objectStore.setActiveObject('catalog', catalog); navigationStore.setDialog('deleteCatalog')">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijder
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="objectStore.isLoading('catalog')"
				class="loadingIcon"
				:size="64"
				appearance="dark"
				name="Zaken aan het laden" />

			<div v-if="!objectStore.getCollection('catalog').results.length" class="emptyListHeader">
				Er zijn nog geen catalogi gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>

<script>
import { NcListItem, NcActionButton, NcAppContentList, NcTextField, NcLoadingIcon, NcActions } from '@nextcloud/vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import DatabaseOutline from 'vue-material-design-icons/DatabaseOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import OpenInApp from 'vue-material-design-icons/OpenInApp.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'

export default {
	name: 'CatalogiList',
	components: {
		NcListItem,
		NcActions,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		NcLoadingIcon,
		HelpCircleOutline,
		DatabaseOutline,
		Magnify,
		Refresh,
		Plus,
		Pencil,
		Delete,
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
    display: flex;
    flex-direction: row;
    align-items: center;
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
