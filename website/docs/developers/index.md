# Design Decisions

## We gebruiken GitHub voor onze code en bestanden

GitHub is een veel gebruikte oplossing binnen Common Ground en biedt ons als open source project voldoende functionaliteit om ons project open en gratis te draaien

## Alle code en documentatie word openbaar gemaakt onder EUPL-licentie

zie [de documentatie](https://documentatie.opencatalogi.nl/)

## We baseren ons op Public Code

### Afwijkingen

- Aan het contract object is de property phone toegevoegd voor het vasthouden van telefoonnummers


## Documentation Features
### Docusaurus
This documentation is built using [Docusaurus](https://docusaurus.io/), a modern static website generator that makes it easy to maintain open source documentation. Docusaurus provides features like:
- Markdown support
- Search functionality 
- Versioning
- Internationalization
- Theme customization
- Plugin system

### Redocusaurus
We use [Redocusaurus](https://redocusaurus.vercel.app/) to integrate OpenAPI/Swagger documentation into our Docusaurus site. This allows us to:
- Display interactive API documentation
- Support OpenAPI 3.0 specifications
- Customize the look and feel
- Keep API docs in sync with the specification

### Mermaid Diagrams
This documentation supports [Mermaid](https://mermaid.js.org/) diagrams for creating flowcharts, sequence diagrams, class diagrams, and more. See the [Mermaid example page](./techniek/mermaid-example.md) for examples and usage instructions.
