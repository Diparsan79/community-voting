# Community Issue Voting Platform

Stack: PHP 8, MySQL, Bootstrap, Vanilla JS

## Prerequisites
- PHP 8+ with pdo_mysql
- MySQL 5.7+/8+

## 1) Configure database
Edit `includes/config.php` if your MySQL credentials differ from defaults:
- `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`

## 2) Create DB schema
Run the schema file in MySQL:

```bash
mysql -u root -p < /workspace/community-issue-voting/sql/schema.sql
```

or inside a MySQL shell:

```sql
SOURCE /workspace/community-issue-voting/sql/schema.sql;
```

## 3) Ensure uploads dir is writable

```bash
mkdir -p /workspace/community-issue-voting/uploads
chmod 777 /workspace/community-issue-voting/uploads
```

## 4) Start the app (choose ONE option)

Option A — Serve parent directory (keep default BASE_URL='/community-issue-voting'):
```bash
php -S localhost:8000 -t /workspace
```
Open: `http://localhost:8000/community-issue-voting/`

Option B — Serve the project directory directly (set BASE_URL='' in `includes/config.php`):
```bash
php -S localhost:8000 -t /workspace/community-issue-voting
```
Open: `http://localhost:8000/`

## 5) Use the app
- Add an issue via “Add Issue”
- Vote on issues (AJAX updates score instantly)
- Open an issue to view/post comments
- Register/Login for named activity; otherwise actions appear as “Guest”

## Notes
- Uploaded images live in `uploads/`
- Guests get a persistent `guest_id` cookie
- This is a prototype; harden security (CSRF, validation, rate limits) before production