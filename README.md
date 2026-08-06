erDiagram
    USERS ||--o{ CLUB_MEMBERS : "has"
    USERS ||--o{ EVENT_REGISTRATIONS : "registers"
    USERS ||--o{ STUDENT_POINTS : "earns"
    
    CLUBS ||--o{ CLUB_MEMBERS : "contains"
    CLUBS ||--o{ EVENTS : "organizes"
    
    EVENT_CATEGORIES ||--o{ EVENTS : "categorizes"
    
    EVENTS ||--o{ EVENT_REGISTRATIONS : "has"
    EVENTS ||--o{ STUDENT_POINTS : "grants"
    
    EVENT_REGISTRATIONS ||--o| CHECKIN_LOGS : "logs"

    USERS {
        bigint id PK
        string name
        string email
        string role "admin, student, manager"
    }
    CLUBS {
        bigint id PK
        string name
        string description
    }
    CLUB_MEMBERS {
        bigint id PK
        bigint club_id FK
        bigint user_id FK
        string role "leader, member"
    }
    EVENT_CATEGORIES {
        bigint id PK
        string name
    }
    EVENTS {
        bigint id PK
        bigint club_id FK
        bigint category_id FK
        string name
        int capacity
        datetime start_time
    }
    EVENT_REGISTRATIONS {
        bigint id PK
        bigint event_id FK
        bigint user_id FK
        string qr_code_string
    }
    CHECKIN_LOGS {
        bigint id PK
        bigint registration_id FK
        datetime checkin_time
    }
    STUDENT_POINTS {
        bigint id PK
        bigint user_id FK
        bigint event_id FK
        int points
    }
```
