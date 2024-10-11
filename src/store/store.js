/* eslint-disable n/no-missing-import */
/* eslint-disable import/no-unresolved */
/* eslint-disable import/extensions */
// fk these rules above here

// The store script handles app wide variables (or state), for the use of these variables and there governing concepts read the design.md
import pinia from '../pinia.js'
import { useCatalogiStore } from './modules/catalogi'
import { useConfigurationStore } from './modules/configuration'
import { useDirectoryStore } from './modules/directory'
import { useNavigationStore } from './modules/navigation'
import { usePublicationTypeStore } from './modules/publicationType'
import { useOrganisationStore } from './modules/organisation'
import { usePublicationStore } from './modules/publication'
import { useSearchStore } from './modules/search'
import { useThemeStore } from './modules/theme'

const navigationStore = useNavigationStore(pinia)
const searchStore = useSearchStore(pinia)
const catalogiStore = useCatalogiStore(pinia)
const directoryStore = useDirectoryStore(pinia)
const publicationTypeStore = usePublicationTypeStore(pinia)
const publicationStore = usePublicationStore(pinia)
const organisationStore = useOrganisationStore(pinia)
const themeStore = useThemeStore(pinia)
const configurationStore = useConfigurationStore(pinia)

export {
	// generic
	navigationStore,
	searchStore,
	// feature-specific
	catalogiStore,
	directoryStore,
	publicationTypeStore,
	publicationStore,
	organisationStore,
	themeStore,
	configurationStore,
}
