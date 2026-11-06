# Database foundation notes

## Core design

- `users` is Laravel's authentication table and is not duplicated.
- `account_profiles` stores common user-facing account data.
- `individual_profiles` stores expert/professional information.
- `organizations` stores organization-level information.
- `organization_users` allows many users to manage one or more organizations with organization-specific membership roles.
- `locations` is a self-referencing hierarchy. Use levels such as `division`, `district`, `upazila`, `union`, `city_corporation`, `municipality`, `ward`, `area`, and `village`.
- `sectors` and `categories` are separate taxonomies. `categories.listing_type` scopes a category tree to organization, service, product or job listings.

## Planned next tables

### Organization and branch directory
- `organization_documents`
- `branches` (organization_id, location_id, name, address, phone, email, latitude, longitude, opening_hours, status)

### Service listings
- `services` (organization_id nullable, individual_profile_id nullable, sector_id, category_id, service_type_id, title, slug, description, price_type, status, published_at)
- `service_locations`
- `service_media`
- `service_inquiries`
- `quotations`

For each service, enforce that exactly one provider owner is supplied (organization or individual profile). Validate this in a Form Request and, where supported by the target MySQL version, with a database CHECK constraint.

### Products
- `products`
- `product_media`
- `product_branch` (pivot)
- `product_stock`
- `orders`
- `order_items`

### Recruitment
- `job_posts`
- `job_requirements`
- `candidate_profiles`
- `candidate_documents`
- `job_applications`

### Platform operations
- `reviews`
- `favorites`
- `notifications` (Laravel standard table)
- `audit_logs`
- `subscription_plans`
- `subscriptions`
- `payments`
- `advertisements`

## Data and integrity rules

- Use foreign keys and indexes on foreign-key columns and common search filters.
- Prefer status columns with controlled values and central constants/enums in application code.
- Use soft deletes for provider-owned content where recovery is useful; do not soft-delete financial or audit records without a retention policy.
- Keep CVs, identity documents and organization verification files on a private storage disk.
- Use database transactions for registration flows that create a user plus profile/organization/membership.
- Use Policies for every organization-owned resource; never rely only on hidden frontend buttons.
