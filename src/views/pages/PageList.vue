/**
 * PageList.vue
 * Component for displaying a list of pages
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogin
 */

<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
</script>

<template>
	<div class="page-list">
		<div class="page-list__header">
			<NcTextField class="page-list__search"
				:value="objectStore.getSearchTerm('page')"
				label="Zoeken"
				trailing-button-icon="close"
				:show-trailing-button="objectStore.getSearchTerm('page') !== ''"
				@update:value="(value) => objectStore.setSearchTerm('page', value)"
				@trailing-button-click="objectStore.clearSearch('page')">
				<template #icon>
					<MagnifyIcon />
				</template>
			</NcTextField>
			<div class="page-list__actions">
				<NcActionButton @click="objectStore.fetchCollection('page')">
					<template #icon>
						<RefreshIcon />
					</template>
					{{ t('opencatalogi', 'Vernieuwen') }}
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('page')">
					<template #icon>
						<PlusIcon />
					</template>
					{{ t('opencatalogi', 'Nieuwe pagina') }}
				</NcActionButton>
			</div>
		</div>
		<NcEmptyContent v-if="!hasPages" :title="t('opencatalogi', 'Geen pagina\'s gevonden')">
			<template #icon>
				<FolderIcon />
			</template>
		</NcEmptyContent>
		<NcLoadingIcon v-else-if="loading" :size="20" />
		<div v-else class="page-list__items">
			<NcListItem v-for="page in pages"
				:key="page.id"
				:title="page.title"
				:subtitle="page.summary"
				:to="'/pages/' + page.id">
				<template #icon>
					<FileIcon />
				</template>
				<template #actions>
					<NcActionButton @click="objectStore.setActiveObject('page', page); navigationStore.setDialog('copyObject', { objectType: 'page', dialogName: 'copyObject', displayName: 'Pagina' })">
						<template #icon>
							<ContentCopyIcon />
						</template>
						{{ t('opencatalogi', 'Kopiëren') }}
					</NcActionButton>
					<NcActionButton @click="objectStore.setActiveObject('page', page); navigationStore.setDialog('deleteObject', { objectType: 'page', dialogName: 'deleteObject', displayName: 'Pagina' })">
						<template #icon>
							<DeleteIcon />
						</template>
						{{ t('opencatalogi', 'Verwijderen') }}
					</NcActionButton>
				</template>
			</NcListItem>
		</div>
	</div>
</template>

<script>
// Components
import { NcEmptyContent, NcLoadingIcon, NcTextField, NcListItem, NcActionButton } from '@nextcloud/vue'

// Icons
import FolderIcon from 'vue-material-design-icons/Folder.vue'
import FileIcon from 'vue-material-design-icons/File.vue'
import ContentCopyIcon from 'vue-material-design-icons/ContentCopy.vue'
import DeleteIcon from 'vue-material-design-icons/Delete.vue'
import MagnifyIcon from 'vue-material-design-icons/Magnify.vue'
import RefreshIcon from 'vue-material-design-icons/Refresh.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'

export default {
	name: 'PageList',
	components: {
		NcEmptyContent,
		NcLoadingIcon,
		NcTextField,
		NcListItem,
		NcActionButton,
		FolderIcon,
		FileIcon,
		ContentCopyIcon,
		DeleteIcon,
		MagnifyIcon,
		RefreshIcon,
		PlusIcon,
	},
	data() {
		return {
			loading: false,
		}
	},
	computed: {
		/**
		 * Get all pages from the store
		 * @return {Array<object>}
		 */
		pages() {
			return objectStore.getCollection('page').results
		},
		/**
		 * Check if there are any pages
		 * @return {boolean}
		 */
		hasPages() {
			return this.pages.length === 0
		},
	},
	mounted() {
		this.fetchData()
	},
	methods: {
		/**
		 * Fetch the page data
		 * @return {Promise<void>}
		 */
		async fetchData() {
			this.loading = true
			try {
				await objectStore.fetchCollection('page')
			} finally {
				this.loading = false
			}
		},
	},
}
</script>

<style scoped>
.page-list {
	padding: 20px;
}

.page-list__header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20px;
}

.page-list__search {
	max-width: 300px;
}

.page-list__actions {
	display: flex;
	gap: 10px;
}

.page-list__items {
	display: flex;
	flex-direction: column;
	gap: 10px;
}
</style>
