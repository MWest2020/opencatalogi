---
sidebar_position: 5
title: Mermaid Diagrams
---

# Mermaid Diagram Examples

This page demonstrates how to use Mermaid diagrams in the documentation.

## Flowchart Example

```mermaid
flowchart TD
    A[Start] --> B{Is it working?}
    B -->|Yes| C[Great!]
    B -->|No| D[Debug]
    D --> B
```

## Sequence Diagram Example

```mermaid
sequenceDiagram
    participant User
    participant OpenCatalogi
    participant API
    participant Database

    User->>OpenCatalogi: Search for publications
    OpenCatalogi->>API: Request data
    API->>Database: Query
    Database-->>API: Return results
    API-->>OpenCatalogi: Format response
    OpenCatalogi-->>User: Display results
```

## Class Diagram Example

```mermaid
classDiagram
    class Publication {
        +String id
        +String title
        +String description
        +Date published
        +publish()
        +archive()
    }
    class Attachment {
        +String id
        +String title
        +String fileType
        +upload()
        +download()
    }
    class Catalog {
        +String id
        +String title
        +addPublication()
        +removePublication()
    }
    Publication "1" *-- "many" Attachment
    Catalog "1" *-- "many" Publication
```

## Entity Relationship Diagram

```mermaid
erDiagram
    CATALOG ||--o{ PUBLICATION : contains
    PUBLICATION ||--o{ ATTACHMENT : has
    PUBLICATION {
        string id
        string title
        string description
        date published
    }
    ATTACHMENT {
        string id
        string title
        string fileType
        string url
    }
    CATALOG {
        string id
        string title
        string description
    }
```

## Gantt Chart Example

```mermaid
gantt
    title OpenCatalogi Development Roadmap
    dateFormat  YYYY-MM-DD
    section Phase 1
    Requirements Analysis   :done,    req1, 2025-01-01, 30d
    Design                  :active,  des1, after req1, 45d
    Development             :         dev1, after des1, 90d
    section Phase 2
    Testing                 :         test1, after dev1, 30d
    Deployment              :         dep1, after test1, 15d
    Documentation           :         doc1, after dep1, 20d
```

## State Diagram Example

```mermaid
stateDiagram-v2
    [*] --> Draft
    Draft --> Review: Submit
    Review --> Draft: Request Changes
    Review --> Published: Approve
    Published --> Archived: Archive
    Archived --> [*]
```

## Using Mermaid in Documentation

To add a Mermaid diagram to your documentation:

1. Create a code block with the `mermaid` language identifier
2. Write your Mermaid diagram syntax inside the code block
3. The diagram will be rendered automatically when the page is displayed

For more information on Mermaid syntax, visit the [Mermaid documentation](https://mermaid.js.org/intro/). 