# Exercise: Nested Comments System with Notifications

### Problem Description
Develop an API in Laravel to manage comments on a post, supporting nested comments (replies to specific comments) up to several levels (parent, child, grandchild, etc.). Additionally, the system should send notifications to the administrator whenever a new comment is added. These notifications should be sent via various channels (such as email and SMS) with the flexibility to scale to additional notification methods, such as real-time desktop notifications.

### Functional Requirements

1. **Nested Comments API:**
   - The API should allow:
     - Creating, editing, listing, and deleting comments.
     - Replying to specific comments, supporting multiple levels of nesting.
   - Each comment should include:
     - Comment text.
     - The parent comment ID (if it’s a reply).
     - Author information.
     - Timestamp.

2. **Notification System:**
   - Implement a system that notifies the administrator whenever a new comment is received.
   - Notifications should be sent via:
     - **Email:** Implement email notifications using Laravel’s Mail feature.
     - **SMS:** Implement SMS notifications using a simulated service (e.g., logging to a file).
   - **Scalability:** The notification architecture should be flexible, allowing the addition of new notification channels, such as real-time desktop notifications (e.g., using WebSockets or Pusher).

3. **Seeder for Sample Comments:**
   - Create a seeder to populate the database with 100 sample comments in a hierarchical structure (parents, children, grandchildren, etc.).
   - Ensure the comments structure supports multiple branches with nested comments (some up to four levels deep).

### Technical Requirements

1. **Model and Eloquent Relationships:**
   - Define the `Comment` model with the necessary relationships to support nested comments (recursive, `hasMany` and `belongsTo`).

2. **Scalable Notification System:**
   - Create a notification service that accepts the comment and channel as inputs, allowing new channels to be added in the future.
   - Use interfaces to define the sending methods, ensuring the system is modular and scalable.

3. **Seeder:**
   - Create a seeder file that generates 100 comments with a hierarchical structure.
   - Include at least three levels of depth in some comment branches.

### Key Evaluation Points

1. **Use of Eloquent Relationships:** Verify the implementation of the hierarchical relationship in the `Comment` model and ensure efficient query structure.
2. **Architecture and Scalability:** Evaluate the flexibility and modularity of the notification system, as well as the use of design patterns to support scalability.
3. **Efficient Seeder:** Ensure the seeder generates the comment structure correctly and efficiently.
4. **Best Practices:** Assess code organization, use of services, and separation of responsibilities.

### Bonus

- **Real-Time Notifications:** Implement (or describe how to implement) real-time notifications for the administrator using Laravel Echo and WebSockets.
- **Unit Tests:** Include unit tests for the comments and notifications system.
