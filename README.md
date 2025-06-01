# AutoSpear: Demonstration of Common Web Application Vulnerabilities

This project, **AutoSpear**, is a small, intentionally built web application developed using **PHP and MySQL**. Its core purpose is to serve as an educational tool, demonstrating prevalent web application security vulnerabilities through practical, hands-on examples. The application's design allows users to explore and understand the mechanisms behind various common attack types.

---

## Project Contents and Structure

The application is structured with several PHP files and a backend MySQL database, each playing a role in showcasing specific vulnerabilities.

### Web Application Files (`.php` files):

* **`index.php`**: This is the application's entry point, primarily serving as the **login page**. It's designed to be vulnerable to both **Brute Force** attacks (due to a lack of rate limiting) and **SQL Injection** (as its authentication query directly concatenates user input).
* **`dashboard.php`**: Accessible after successful login, this page features a product search function. The search functionality is intentionally built with a **SQL Injection** vulnerability, allowing attackers to manipulate the database query for data exfiltration or unauthorized data access. This page also hosts the comments section, which is vulnerable to **Cross-Site Scripting (XSS)**.
* **`comment.php`**: This file handles the submission of user comments. It stores the comment text directly into the database without sufficient sanitization, setting up the **XSS** vulnerability that manifests when comments are displayed on `dashboard.php`.
* **`db_connect.php`**: This file contains the database connection parameters (server, username, password, database name). It establishes the link between the PHP application and the MySQL database. For the purpose of this demonstration, default XAMPP credentials (`root` with no password) are used.
* **`logout.php`**: A simple script to end the user's session and redirect them back to the login page.

### Database Structure (MySQL - `vulnerable_app`):

The project utilizes a MySQL database named `vulnerable_app` with the following tables:

* **`users` Table**:
    * Stores user credentials (`username`, `password`).
    * Contains a sample `admin` user with a deliberately weak password (`password123`) to facilitate the **Brute Force** demonstration.
* **`products` Table**:
    * Holds sample product information (`name`, `description`, `price`).
    * Used by the vulnerable search function on `dashboard.php` to demonstrate **SQL Injection** for data retrieval.
* **`comments` Table**:
    * Stores user-submitted comments (`username`, `comment_text`, `timestamp`).
    * Content from this table is displayed directly on `dashboard.php`, serving as the focal point for the **Cross-Site Scripting (XSS)** demonstrations.

---

## Demonstrating the Attacks

By interacting with these components, users can explore:

* **SQL Injection**: How an attacker can bypass login, or extract/manipulate database information (e.g., product details, user credentials) using crafted input.
* **Cross-Site Scripting (XSS)**: How malicious client-side scripts can be injected into the comment section, leading to pop-up alerts, simulated cookie theft, or page redirection in the victim's browser.
* **Brute Force**: How an automated script can systematically guess login credentials without being blocked, eventually gaining unauthorized access.

---

## Purpose and Safe Usage

This application is designed to provide a hands-on, practical understanding of these prevalent web security threats. By actively demonstrating how these attacks work, users can gain valuable insight into their mechanisms, understand their potential impact, and, most importantly, learn how to implement secure coding practices to prevent them in real-world applications.

**Important Note:** This application is intentionally vulnerable and is developed purely for educational purposes. It is **NOT** suitable for production use under any circumstances and should **NEVER** be deployed publicly or used with real or sensitive data. Always run this project in a controlled, isolated local environment (e.g., using XAMPP or Docker).
