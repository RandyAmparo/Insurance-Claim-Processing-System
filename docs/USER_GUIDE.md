# Insurance Claim Processing System — User Guide

This guide is for employees using the application. For installation and database setup, see [README.md](docs/README.md) and [Demo_Insurance_Claim_System.mp4](docs/Demo_Insurance_Claim_System.mp4).

## 1. Sign in

1. Open [the login page](http://localhost/insurance_project/index.html).
2. Enter your assigned test employee email and password.
3. Submit the form. The main menu displays links to file, review, and view claims.


## 2. File a new claim

1. Select **File a New Claim** from the main menu.
2. Enter the customer's name, address, and phone number.
3. Choose the coverage type and fill in the remaining policy details shown on the form.
4. Enter the incident date, claim amount, and a short description of what happened. For the demo, use fictional details and an incident date within the policy's coverage period.
5. Submit the form, then return to the menu. Open **Approve / Deny Claims** and confirm the new claim appears.


## 3. Approve or deny a claim

1. Select **Approve / Deny Claims**.
2. Find the correct claim by its ID and customer name. Review its description, incident date, and amount.
3. Click **Approve** to approve it and record a payment, or **Deny** to deny it without a payment.
4. Check that the reviewed claim no longer appears in the pending list.


## 4. View the decision and log out

1. Return to the menu and select **View Claims (Approved / Denied)**.
2. Find the claim by its ID and check the decision, decision date, and reviewer.
3. Return to the menu and select **Log Out** when finished.


## If something goes wrong

- **Page does not open:** Confirm Apache is running and that your URL uses the folder name under XAMPP's htdocs directory.
- **Sign-in fails:** Use a test employee account from the imported data. The MySQL password is used by PHP to connect to the database.
- **Claim is missing from the pending list:** Check whether it already appears in View Claims. Only claims under review appear on the pending page.
