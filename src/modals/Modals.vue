<script setup>
import { navigationStore, publicationStore } from './../store/store.js'
</script>

<template>
	<!-- Placeholder Div -->
	<div>
		<AddAttachmentModal :drop-files="publicationStore.attachmentFile" />
		<EditAttachmentModal />
		<PublicationModal v-if="navigationStore.modal === 'publication'" />
		<CatalogModal v-if="navigationStore.modal === 'catalog'" />
		<AddCatalogiPublicationType />
		<AddDirectoryModal />
		<EditListingModal />
		<AddPublicationDataModal />
		<EditPublicationDataModal />
		<OrganizationModal />
		<ThemeModal v-if="navigationStore.modal === 'theme'" />
		<PageModal v-if="navigationStore.modal === 'page'" />
		<DeletePage v-if="navigationStore.modal === 'deletePage'" />
		<AddPageContentsModal v-if="navigationStore.modal === 'addPageContents'" />
		<!-- Menu -->
		<EditMenuModal v-if="navigationStore.modal === 'editMenu'" />
		<DeleteMenuModal />
		<EditMenuItemModal v-if="navigationStore.modal === 'editMenuItem'" />
		<DeleteMenuItemModal v-if="navigationStore.modal === 'deleteMenuItem'" />
	</div>
</template>

<script>
import AddAttachmentModal from './attachment/AddAttachmentModal.vue'
import EditAttachmentModal from './attachment/EditAttachmentModal.vue'
import PublicationModal from './publication/PublicationModal.vue'

import CatalogModal from './catalog/CatalogModal.vue'
import AddCatalogiPublicationType from './catalogiPublicationType/AddCatalogiPublicationType.vue'
import AddDirectoryModal from './directory/AddDirectoryModal.vue'
import EditListingModal from './directory/EditListingModal.vue'
import OrganizationModal from './organization/OrganizationModal.vue'
import AddPublicationDataModal from './publicationData/AddPublicationDataModal.vue'
import EditPublicationDataModal from './publicationData/EditPublicationDataModal.vue'
import ThemeModal from './theme/ThemeModal.vue'
import PageModal from './page/PageModal.vue'
import DeletePage from './page/DeletePage.vue'
import AddPageContentsModal from './pageContents/AddPageContents.vue'
// menu
import EditMenuModal from './menu/EditMenuModal.vue'
import DeleteMenuModal from './menu/DeleteMenuModal.vue'
import EditMenuItemModal from './menuItem/EditMenuItemModal.vue'
import DeleteMenuItemModal from './menuItem/DeleteMenuItemModal.vue'

/**
 * Component that contains all modals used in the application
 */
export default {
	name: 'Modals',
	components: {
		AddAttachmentModal,
		EditAttachmentModal,
		PublicationModal,
		CatalogModal,
		AddCatalogiPublicationType,
		AddDirectoryModal,
		EditListingModal,
		AddPublicationDataModal,
		EditPublicationDataModal,
		OrganizationModal,
		ThemeModal,
		PageModal,
		DeletePage,
		AddPageContentsModal,
		// menu
		EditMenuModal,
		DeleteMenuModal,
		EditMenuItemModal,
		DeleteMenuItemModal,
	},
}
</script>
