<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<NcAppContentList>
		<ul>
			<div class="listHeader">
				<NcTextField class="searchField"
					:value="objectStore.getSearchTerm('page')"
					label="Zoeken"
					trailing-button-icon="close"
					:show-trailing-button="objectStore.getSearchTerm('page') !== ''"
					@update:value="(value) => objectStore.setSearchTerm('page', value)"
					@trailing-button-click="objectStore.clearSearch('page')">
					<Magnify :size="20" />
				</NcTextField>
				<NcActionButton class="refresh" @click="objectStore.fetchCollection('page')">
					<template #icon>
						<Refresh :size="20" />
					</template>
				</NcActionButton>
				<NcActionButton class="refresh" @click="navigationStore.setModal('pageForm')">
					<template #icon>
						<Plus :size="20" />
					</template>
				</NcActionButton>
			</div>
			<div v-if="!objectStore.isLoading('page')">
				<NcListItem v-for="(page, i) in objectStore.getCollection('page').results"
					:key="`${page}${i}`"
					:name="page.name"
					:bold="false"
					:force-display-actions="true"
					:active="objectStore.getActiveObject('page')?.id === page.id"
					:details="page?.status"
					@click="objectStore.setActiveObject('page', page)">
					<template #icon>
						<Web :size="44" />
					</template>
					<template #subname>
						{{ page?.slug }}
					</template>
					<template #actions>
						<NcActionButton @click="objectStore.setActiveObject('page', page); navigationStore.setModal('pageForm')">
							<template #icon>
								<Pencil :size="20" />
							</template>
							Bewerken
						</NcActionButton>
						<NcActionButton @click="objectStore.setActiveObject('page', page); navigationStore.setDialog('copyPage')">
							<template #icon>
								<ContentCopy :size="20" />
							</template>
							Kopiëren
						</NcActionButton>
						<NcActionButton @click="objectStore.setActiveObject('page', page); navigationStore.setModal('deletePage')">
							<template #icon>
								<Delete :size="20" />
							</template>
							Verwijderen
						</NcActionButton>
					</template>
				</NcListItem>
			</div>

			<NcLoadingIcon v-if="objectStore.isLoading('page')"
				:size="64"
				class="loadingIcon"
				appearance="dark"
				name="Paginas aan het laden" />

			<div v-if="!objectStore.getCollection('page').results.length" class="emptyListHeader">
				Er zijn nog geen pagina's gedefinieerd.
			</div>
		</ul>
	</NcAppContentList>
</template>

<script>
import { NcActionButton, NcAppContentList, NcListItem, NcLoadingIcon, NcTextField } from '@nextcloud/vue'

// Icons
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Refresh from 'vue-material-design-icons/Refresh.vue'
import Web from 'vue-material-design-icons/Web.vue'

export default {
	name: 'PageList',
	components: {
		NcListItem,
		NcActionButton,
		NcAppContentList,
		NcTextField,
		Magnify,
		NcLoadingIcon,
		// Icons
		Refresh,
		Plus,
		ContentCopy,
		Web,
		Pencil,
	},
	methods: {
		openLink(url, type = '') {
			window.open(url, type)
		},
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

.active.pageDetails-actionsDelete {
    background-color: var(--color-error) !important;
}
.active.pageDetails-actionsDelete button {
    color: #EBEBEB !important;
}

.loadingIcon {
    margin-block-start: var(--OC-margin-20);
}
</style>
