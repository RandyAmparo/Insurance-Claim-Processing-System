# Insurance Claim Processing System

A CIS 344 group project built with PHP, HTML, CSS, and MySQL. Employees can sign in, file insurance claims, review pending claims, and view approved and denied claims. Approving a claim also records its payment.

## Run locally with XAMPP on Windows

1. Download or clone this repository. Copy the application files into `C:\xampp\htdocs\insurance_project\`. Keep the PHP, HTML, and CSS files together, with `index.html` directly inside that folder.
2. Open the XAMPP Control Panel and start **Apache** and **MySQL**.
3. Open [phpMyAdmin](http://localhost/phpmyadmin/) and use **Import** to load the project's final SQL setup script. The HTML, PHP, and CSS files alone do not create the database. If the SQL script is missing, get a copy of the final schema and sample data from the project team. Check for `DROP DATABASE` before importing over an existing database.
4. Confirm the database is named `insurance_firm_344`. Check the MySQL connection settings in `login.php`, `add_claim.php`, `pending_claims.php`, and `view_claims.php`. The original settings use `localhost`, user `root`, and a blank database password. If your local server uses different credentials or a different port, update all four local copies.
5. Open [the login page](http://localhost/insurance_project/index.html) and sign in with a test employee account from your imported sample data. The employee login and MySQL credentials are separate. If Apache runs on a different port, include that port in the localhost URLs.

Use fictional accounts and sample claim data. Keep real passwords and local database credentials out of the public repository.

## Main pages

| File | Purpose |
| --- | --- |
| `index.html` and `login.php` | Display the sign-in form and verify the employee login |
| `menu.php` | Show the employee menu |
| `add_claim.php` | File a claim and save the associated customer and policy information |
| `pending_claims.php` | List claims under review and process approval or denial |
| `view_claims.php` | Show approved and denied claims and their reviewer |
| `logout.php` | End the employee session |

## Database design and joins

| Table | Primary key | Relationships |
| --- | --- | --- |
| `employees` | `employee_id` | Staff accounts and claim reviewers |
| `customers` | `customer_id` | Customer contact information |
| `policies` | `policy_id` | `customer_id` references `customers.customer_id` |
| `claims` | `claim_id` | `policy_id` references `policies.policy_id`; `reviewed_by` references `employees.employee_id` |
| `payments` | `payment_id` | `claim_id` references `claims.claim_id` |

The pending claims page uses a three-table join to display the customer's name and policy coverage alongside each claim:

```sql
SELECT Claims.claim_id, Customers.first_name, Customers.last_name,
       Policies.coverage_type, Claims.claim_amount
FROM Claims
JOIN Policies ON Claims.policy_id = Policies.policy_id
JOIN Customers ON Policies.customer_id = Customers.customer_id
WHERE Claims.decision_status = 'Under Review';
```

The first join finds the policy for each claim. The second finds the customer who owns that policy. The completed claims page also uses a `LEFT JOIN` to `Employees` to retrieve the reviewer's name while keeping claims without a matching reviewer in the results.

The PHP inserts expect the IDs for customers, policies, claims, and payments to be generated automatically. New claims use `Under Review` and leave the reviewer and decision date unset until a decision is made.

## Test the workflow

1. Sign in with a test employee account and select **File a New Claim**.
2. Submit a claim using fictional customer and policy details. Confirm it appears under **Approve / Deny Claims**.
3. Approve the claim. Confirm it appears under **View Claims** as approved and has a matching record in the `payments` table in phpMyAdmin. Approval and payment are saved together in a database transaction.
4. File a second claim and deny it. Confirm it appears as denied and has no payment record.
5. Log out and verify that opening the menu again requires signing in.
