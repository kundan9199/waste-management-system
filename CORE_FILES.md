# CORE FILES REFERENCE

---

## MODELS (app/Models/)

File: app/Models/User.php
Use: Stores user login info and their role (admin or user). Controls who can access what.

File: app/Models/PickupRequest.php
Use: Stores every pickup request made by a user. Has the status (pending, in_progress, completed, cancelled) and links to categories, complaints, and feedback.

File: app/Models/WasteCategory.php
Use: Stores waste types (like Plastic, E-Waste). Each category has a name and color. Linked to pickup requests.

File: app/Models/Complaint.php
Use: Stores complaints raised by users. Has a status (open, in_review, resolved, closed) and stores the admin's reply.

File: app/Models/Feedback.php
Use: Stores star ratings and messages left by users after a completed request.

---

## CONTROLLERS - ADMIN (app/Http/Controllers/Admin/)

File: app/Http/Controllers/Admin/DashboardController.php
Use: Loads the admin home page with total counts (requests, complaints, etc.) and recent activity.

File: app/Http/Controllers/Admin/PickupRequestController.php
Use: Lists all pickup requests with search and filter. Allows admin to update status, view details, delete, and export a PDF.

File: app/Http/Controllers/Admin/ComplaintController.php
Use: Lists all complaints. Admin can open a complaint, write a response, and change its status.

File: app/Http/Controllers/Admin/FeedbackController.php
Use: Lists all user feedback with average rating. Admin can hide or publish individual feedback entries.

File: app/Http/Controllers/Admin/WasteCategoryController.php
Use: Full CRUD for waste categories. Cannot delete a category that has existing requests.

File: app/Http/Controllers/Admin/UserController.php
Use: View list of all registered users and individual user details.

---

## CONTROLLERS - USER (app/Http/Controllers/User/)

File: app/Http/Controllers/User/DashboardController.php
Use: Loads the user home page with their personal counts (total, pending, completed requests).

File: app/Http/Controllers/User/PickupRequestController.php
Use: Lets users create a new pickup request (with image upload and category selection) and view their own requests.

File: app/Http/Controllers/User/ComplaintController.php
Use: Lets users raise a complaint linked to a request, and view their own complaints and admin responses.

File: app/Http/Controllers/User/FeedbackController.php
Use: Lets users leave a star rating and message for a completed request, and view their past feedback.

---

## ROUTES (routes/)

File: routes/web.php
Use: Defines all URL routes. Groups admin routes under /admin with admin middleware, and user routes under /user. This is where every URL is registered.

---

## VIEWS - BLADE TEMPLATES

### Layout Files (resources/views/layouts/)

File: resources/views/layouts/app.blade.php
Use: Main layout wrapper used by all pages. Includes the navbar and wraps all page content.

File: resources/views/layouts/navigation.blade.php
Use: The top navigation bar shown on all pages. Shows different links for admin vs user.

---

### Admin Views (resources/views/admin/)

File: resources/views/admin/dashboard.blade.php
Use: Admin home page showing stat cards and tables of recent requests and open complaints.

File: resources/views/admin/requests/index.blade.php
Use: Table of all requests. Has search, filter by status and category, and a PDF export button.

File: resources/views/admin/requests/show.blade.php
Use: Full details of one request. Admin can update status and write internal notes here.

File: resources/views/admin/requests/pdf.blade.php
Use: Special view used only for generating the PDF export of requests.

File: resources/views/admin/complaints/index.blade.php
Use: Table of all complaints with status filter.

File: resources/views/admin/complaints/show.blade.php
Use: Shows one complaint in detail. Admin writes a response and updates the status here.

File: resources/views/admin/feedbacks/index.blade.php
Use: Shows all feedback with average rating. Toggle button to publish or hide each entry.

File: resources/views/admin/categories/index.blade.php
Use: Lists all waste categories with edit and delete options.

File: resources/views/admin/categories/create.blade.php
Use: Form to create a new waste category with name, color picker, and description.

File: resources/views/admin/categories/edit.blade.php
Use: Same form as create but pre-filled for editing an existing category.

---

### User Views (resources/views/user/)

File: resources/views/user/dashboard.blade.php
Use: User home page with their personal stats and a table of their recent requests.

File: resources/views/user/requests/index.blade.php
Use: Table of all the user's own requests with search and status filter.

File: resources/views/user/requests/create.blade.php
Use: Form to submit a new pickup request. User picks categories, date, address, and uploads an image.

File: resources/views/user/requests/show.blade.php
Use: Full details of one request. Shows status, schedule, image, and admin notes. Also shows feedback prompt and raise-complaint button.

File: resources/views/user/complaints/index.blade.php
Use: Table of all complaints raised by this user.

File: resources/views/user/complaints/create.blade.php
Use: Form to raise a new complaint, optionally linked to a specific request.

File: resources/views/user/complaints/show.blade.php
Use: Shows one complaint and the admin's response if it has been replied to.

File: resources/views/user/feedbacks/index.blade.php
Use: Card grid showing all feedback the user has submitted.

File: resources/views/user/feedbacks/create.blade.php
Use: Form to leave a star rating and message for a completed pickup request.

---

### Reusable Components (resources/views/components/)

File: resources/views/components/status-badge.blade.php
Use: Shows a coloured pill badge for any status (pending, completed, open, etc.). Used everywhere instead of repeating the same inline span code.

File: resources/views/components/category-badge.blade.php
Use: Shows a coloured label for a waste category using its saved hex color. Used in all request tables and detail pages.

File: resources/views/components/star-rating.blade.php
Use: Renders 1 to 5 filled/empty star icons. Used in feedback views instead of repeating SVG loops.

File: resources/views/components/stat-card.blade.php
Use: A dashboard stat box showing a number, label, and icon. Used on both the admin and user dashboards.

---

## MAIL (app/Mail/)

File: app/Mail/RequestStatusUpdated.php
Use: Email sent automatically to the user whenever an admin updates the status of their pickup request.

---

## MIDDLEWARE (app/Http/Middleware/)

File: app/Http/Middleware/AdminMiddleware.php
Use: Checks if the logged-in user is an admin. If not, redirects them away from admin routes.

---

## MIGRATIONS (database/migrations/)

These files define the database table structure. Key ones:
- create_users_table — user accounts
- create_pickup_requests_table — all pickup request data
- create_waste_categories_table — category definitions
- create_pickup_request_waste_category_table — pivot linking requests to categories
- create_complaints_table — complaints data
- create_feedbacks_table — feedback and ratings
