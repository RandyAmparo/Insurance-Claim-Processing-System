CREATE DATABASE insurance_firm_344;
USE insurance_firm_344;

CREATE TABLE employees(
employee_id INT(7) PRIMARY KEY NOT NULL UNIQUE, 
first_name VARCHAR(45) NOT NULL,
last_name VARCHAR(45) NOT NULL, 
email VARCHAR(100) NOT NULL,
e_password  VARCHAR(45) NOT NULL 
);
CREATE TABLE customers(
customer_id INT(7) PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
first_name VARCHAR(45)NOT NULL,
last_name VARCHAR(45) NOT NULL,
address VARCHAR(45) NOT NULL,
c_number VARCHAR(12) NOT NULL,
join_date DATE NOT NULL
);
CREATE TABLE policies(
policy_id INT(7) PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
customer_id INT(7) NOT NULL UNIQUE,
coverage_type VARCHAR(45) NOT NULL,
coverage_limit INT NOT NULL,
start_date DATE NOT NULL,
end_date DATE NOT NULL,

FOREIGN KEY (customer_id)
REFERENCES customers(customer_id)
);
CREATE TABLE claims(
claim_id INT(7) PRIMARY KEY NOT NULL UNIQUE AUTO_INCREMENT,
policy_id INT(7) NOT NULL,
reviewed_by INT NULL,
incident_date DATE NOT NULL,
cl_description TEXT NOT NULL,
claim_amount DECIMAL(12,2) NOT NULL,
decision_date DATE NULL,
decision_status VARCHAR(45) NOT NULL,

FOREIGN KEY (policy_id) 
REFERENCES policies(policy_id),

FOREIGN KEY (reviewed_by)
REFERENCES employees(employee_id)
);

CREATE TABLE payments(
payment_id INT(7) PRIMARY KEY NOT NULL UNIQUE,
claim_id INT NOT NULL,
payment_amount DECIMAL(12,2) NOT NULL,
payment_date DATE NOT NULL,

FOREIGN KEY (claim_id)
REFERENCES claims(claim_id)
);

INSERT INTO employees(employee_id, first_name, last_name, email, e_password)
VALUES
	(7405823, 'Randy', 'Amparo', 'randy.amparo@lehman.cuny.edu', 'goodmorning123'),
    (7147513, 'Amerie', 'Rodriguez', 'amerie.rogeriguez@lehman.cuny.edu', 'goodnight123'),
    (7245743, 'Pascal', 'Aiddou', 'pascal.aiddou88@lehman.cuny.edu', 'goodafternoon123');
    
INSERT INTO customers(customer_id, first_name, last_name, address, c_number, join_date)
VALUES
    (1000001, 'Daniel', 'Rivera', '125 Maple Ave', '202-555-0101', '2025-01-15'),
    (1000002, 'Sofia', 'Chen', '48 Oak Street', '202-555-0102', '2025-02-20'),
    (1000003, 'Marcus', 'Johnson', '310 Pine Road', '202-555-0103', '2025-03-10'),
    (1000004, 'Elena', 'Torres', '76 Cedar Lane', '202-555-0104', '2025-04-05'),
    (1000005, 'James', 'Patel', '902 Birch Drive', '202-555-0105', '2025-05-12');


INSERT INTO policies(policy_id, customer_id, coverage_type, coverage_limit, start_date, end_date)
VALUES
    (2000001, 1000001, 'Auto Basic',     25000, '2026-01-01', '2026-12-31'),
    (2000002, 1000002, 'Home Standard', 150000, '2026-02-01', '2027-01-31'),
    (2000003, 1000003, 'Auto Premium',   75000, '2026-03-01', '2027-02-28'),
    (2000004, 1000004, 'Renters Basic',  20000, '2026-04-01', '2027-03-31'),
    (2000005, 1000005, 'Home Premium',  300000, '2026-05-01', '2027-04-30');


INSERT INTO claims(claim_id, policy_id, reviewed_by, incident_date, cl_description, claim_amount, decision_date, decision_status)
VALUES
    (3000001, 2000001, 7405823, '2026-06-12',
     'Rear bumper damaged in a parking collision.',
     2400.00, '2026-06-16', 'Approved'),

    (3000002, 2000002, 7147513, '2026-07-03',
     'Kitchen ceiling damaged by a leaking pipe.',
     6800.00, '2026-07-09', 'Denied'),

    (3000003, 2000003, 7245743, '2026-07-21',
     'Vehicle windshield cracked by road debris.',
     950.10, '2026-07-24', 'Under Review'),

    (3000004, 2000004, 7147513, '2026-08-14',
     'Personal belongings stolen during an apartment burglary.',
     3200.21, '2026-08-20', 'Under Review'),

    (3000005, 2000005, 7405823, '2026-09-08',
     'Roof shingles damaged during a windstorm.',
     12500.49, '2026-09-15', 'Approved');


INSERT INTO payments(payment_id, claim_id, payment_amount, payment_date)
VALUES
    (4000001, 3000001, 2400.00,  '2026-06-18'),
    (4000002, 3000002, 6800.00,  '2026-07-12'),
    (4000003, 3000003, 950.10,   '2026-07-27'),
    (4000004, 3000004, 3200.21,  '2026-08-23'),
    (4000005, 3000005, 12500.49, '2026-09-18');
    
SELECT * FROM employees;
SELECT * FROM customers;
SELECT * FROM policies;
SELECT * FROM claims;
SELECT * FROM payments;