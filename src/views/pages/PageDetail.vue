/**
 * PageDetail.vue
 * Component for displaying page details
 * @category Components
 * @package opencatalogi
 * @author Ruben Linde
 * @copyright 2024
 * @license AGPL-3.0-or-later
 * @version 1.0.0
 * @link https://github.com/opencatalogi/opencatalogi
 */

<script setup>
import { navigationStore, objectStore } from '../../store/store.js'
import { getTheme } from '../../services/getTheme.js'
import { EventBus } from '../../eventBus.js'
</script>

<template>
	<div class="page-detail">
		<div class="page-detail__header">
			<h1>{{ page?.name }}</h1>
			<NcActions
				:disabled="loading"
				:primary="true"
				:menu-name="loading ? t('opencatalogi', 'Laden...') : t('opencatalogi', 'Acties')"
				:inline="1"
				:title="t('opencatalogi', 'Acties die je kan uitvoeren op deze pagina')">
				<template #icon>
					<span>
						<NcLoadingIcon v-if="loading" :size="20" appearance="dark" />
						<DotsHorizontal v-if="!loading" :size="20" />
					</span>
				</template>
				<NcActionButton
					:title="t('opencatalogi', 'Bekijk de documentatie over paginas')"
					@click="openLink('https://conduction.gitbook.io/opencatalogi-nextcloud/beheerders/paginas')">
					<template #icon>
						<HelpCircleOutline :size="20" />
					</template>
					{{ t('opencatalogi', 'Help') }}
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('page')">
					<template #icon>
						<Pencil :size="20" />
					</template>
					{{ t('opencatalogi', 'Bewerken') }}
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('copyObject', { objectType: 'page', dialogName: 'copyObject', dialogTitle: 'Pagina' })">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					{{ t('opencatalogi', 'Kopiëren') }}
				</NcActionButton>
				<NcActionButton @click="navigationStore.setModal('addPageContents')">
					<template #icon>
						<Plus :size="20" />
					</template>
					{{ t('opencatalogi', 'Content toevoegen') }}
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('copyObject', { objectType: 'page', dialogTitle: 'Page'})">
					<template #icon>
						<ContentCopy :size="20" />
					</template>
					Kopiëren
				</NcActionButton>
				<NcActionButton @click="navigationStore.setDialog('deleteObject', { objectType: 'page', dialogTitle: 'Page'})">
					<template #icon>
						<Delete :size="20" />
					</template>
					Verwijderen
				</NcActionButton>
			</NcActions>
		</div>
		<div class="page-detail__content">
			<div class="page-detail__grid">
				<div>
					<b>{{ t('opencatalogi', 'Naam') }}:</b>
					<span>{{ page?.name }}</span>
				</div>
				<div>
					<b>{{ t('opencatalogi', 'Slug') }}:</b>
					<span>{{ page?.slug }}</span>
				</div>
				<div>
					<b>{{ t('opencatalogi', 'Laatst bijgewerkt') }}:</b>
					<span>{{ page?.updatedAt ? new Date(page.updatedAt).toLocaleDateString() : '-' }}</span>
				</div>
			</div>
		</div>
		<div class="page-detail__tabs">
			<BTabs content-class="mt-3" justified>
				<BTab active>
					<template #title>
						<div class="page-detail__tab-title">
							<p>{{ t('opencatalogi', 'Data') }}</p>
							<NcLoadingIcon v-if="saveContentsLoading" class="page-detail__tab-icon" :size="24" />
							<CheckCircleOutline v-if="saveContentsSuccess" class="page-detail__tab-icon" :size="24" />
						</div>
					</template>
					<div v-if="pageContents.length > 0">
						<VueDraggable v-model="pageContents" easing="ease-in-out">
							<div v-for="(pageContent, i) in pageContents"
								:key="i"
								:class="`page-detail__draggable-item ${getTheme()}`">
								<Drag class="page-detail__drag-handle" :size="40" />
								<NcListItem :name="pageContent.type"
									:bold="false"
									:force-display-actions="true">
									<template #subname>
										{{ JSON.stringify(pageContent.data) }}
									</template>
									<template #actions>
										<NcActionButton :disabled="saveContentsLoading"
											@click="objectStore.contentId = pageContent.id; navigationStore.setModal('addPageContents')">
											<template #icon>
												<Pencil :size="20" />
											</template>
											{{ t('opencatalogi', 'Bewerken') }}
										</NcActionButton>
										<NcActionButton :disabled="saveContentsLoading"
											@click="deleteContent(pageContent.id)">
											<template #icon>
												<Delete :size="20" />
											</template>
											{{ t('opencatalogi', 'Verwijderen') }}
										</NcActionButton>
									</template>
								</NcListItem>
							</div>
						</VueDraggable>
						<NcButton :disabled="(JSON.stringify(page?.contents) === JSON.stringify(pageContents)) || saveContentsLoading"
							@click="savePageContents">
							{{ t('opencatalogi', 'Opslaan') }}
						</NcButton>
					</div>
					<div v-else>
						{{ t('opencatalogi', 'Geen page content gevonden') }}
					</div>
				</BTab>
			</BTabs>
		</div>
	</div>
</template>

<script>
// Components
import { NcActionButton, NcActions, NcLoadingIcon, NcListItem, NcButton } from '@nextcloud/vue'
import { VueDraggable } from 'vue-draggable-plus'
import { BTabs, BTab } from 'bootstrap-vue'
import { Page } from '../../entities/index.js'

// Icons
import ContentCopy from 'vue-material-design-icons/ContentCopy.vue'
import Delete from 'vue-material-design-icons/Delete.vue'
import DotsHorizontal from 'vue-material-design-icons/DotsHorizontal.vue'
import HelpCircleOutline from 'vue-material-design-icons/HelpCircleOutline.vue'
import Pencil from 'vue-material-design-icons/Pencil.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Drag from 'vue-material-design-icons/Drag.vue'
import CheckCircleOutline from 'vue-material-design-icons/CheckCircleOutline.vue'
import _ from 'lodash'

export default {
	name: 'PageDetail',
	components: {
		NcLoadingIcon,
		NcActionButton,
		NcActions,
		NcButton,
		VueDraggable,
		BTabs,
		BTab,
		DotsHorizontal,
		Pencil,
		Delete,
		ContentCopy,
		HelpCircleOutline,
		Drag,
		CheckCircleOutline,
		Plus,
	},
	data() {
		return {
			pageContents: [],
			saveContentsLoading: false,
			saveContentsSuccess: false,
			loading: false,
		}
	},
	computed: {
		/**
		 * Get the current page from the store
		 * @return {object | null}
		 */
		page() {
			return objectStore.getActiveObject('page')
		},
	},
	watch: {
		/**
		 * Watch for changes in the page ID to fetch updated data
		 * @param {string} newId - The new page ID
		 * @return {void}
		 */
		'page.id': {
			handler(newId) {
				if (newId) {
					this.fetchData()
				}
			},
			immediate: true,
		},
	},
	created() {
		EventBus.$on(['edit-page-content-success', 'delete-page-content-success'], () => {
			this.fetchData()
		})
	},
	beforeDestroy() {
		EventBus.$off(['edit-page-content-success', 'delete-page-content-success'])
	},
	mounted() {
		this.pageContents = this.page?.contents || []
		this.fetchData()
	},
	methods: {
		/**
		 * Fetch the page data
		 * @return {Promise<void>}
		 */
		async fetchData() {
			if (!this.page?.id) return
			this.loading = true
			try {
				const { data } = await objectStore.fetchObject('page', this.page.id)
				this.pageContents = data.contents
			} finally {
				this.loading = false
			}
		},
		/**
		 * Delete a content item
		 * @param {string} contentId - The ID of the content to delete
		 * @return {Promise<void>}
		 */
		async deleteContent(contentId) {
			if (!this.page) return
			this.loading = true
			try {
				const newContents = this.page.contents.filter((content) => content.id !== contentId)
				const newPageItem = new Page({
					...this.page,
					contents: newContents,
				})
				await objectStore.updateObject('page', newPageItem)
				await this.fetchData()
			} finally {
				this.loading = false
			}
		},
		/**
		 * Save the page contents
		 * @return {Promise<void>}
		 */
		async savePageContents() {
			if (!this.page) return
			this.saveContentsLoading = true
			this.saveContentsSuccess = false
			try {
				const pageItemClone = _.cloneDeep(this.page)
				pageItemClone.contents = this.pageContents
				const newPageItem = new Page(pageItemClone)
				await objectStore.updateObject('page', newPageItem)
				this.saveContentsSuccess = true
				setTimeout(() => {
					this.saveContentsSuccess = false
				}, 1500)
			} finally {
				this.saveContentsLoading = false
			}
		},
		/**
		 * Open a link in a new window
		 * @param {string} url - The URL to open
		 * @param {string} [type] - The window type
		 * @return {void}
		 */
		openLink(url, type = '') {
			window.open(url, type)
		},
	},
}
</script>

<style scoped>
.page-detail {
	padding: 20px;
}

.page-detail__header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 20px;
}

.page-detail__content {
	margin-bottom: 20px;
}

.page-detail__grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 20px;
}

.page-detail__tab-title {
	display: flex;
	justify-content: center;
	align-items: center;
	position: relative;
}

.page-detail__tab-icon {
	position: absolute;
	right: 0;
}

.page-detail__draggable-item {
	display: flex;
	align-items: center;
	gap: 3px;
	background-color: rgba(255, 255, 255, 0.05);
	padding: 4px;
	border-radius: 8px;
	margin-block: 8px;
}

.page-detail__draggable-item.light {
	background-color: rgba(0, 0, 0, 0.05);
}

.page-detail__drag-handle {
	cursor: move;
}
</style>
