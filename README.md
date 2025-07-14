# BillTracker

> A lightweight web application to keep track of recurring bills and their payment status. Built with **vanilla PHP 8**, **MySQL**, and a touch of **HTML/CSS/JavaScript**—perfect for showcasing full-stack fundamentals to prospective employers.

---

## Key Features

* **User authentication** – secure login/logout with role-based access (admin & standard user)
* **CRUD bills** – add, edit, delete, and view upcoming bills
* **One-click paid/unpaid toggle** – updates status instantly via AJAX
* **Automatic totals** – monthly and yearly spend at a glance
* **Bootstrap-styled UI** – clean, responsive layout


---

## Tech Stack

| Layer         | What we used                                |
| ------------- | ------------------------------------------- |
| **Backend**   | PHP 8, MySQL 8                              |
| **Frontend**  | HTML5, CSS3 (Bootstrap 5), JavaScript (ES6) |
| **Dev Tools** | Git, VS Code, PHP CLI, MySQL Workbench      |

---

## Quick Start (Local)

```bash
# 1. Clone the repo
git clone https://github.com/anoop-singh02/billtracker.git
cd billtracker

# 2. Spin up MySQL (Docker) – optional but easiest
docker compose up -d db

# 3. Import sample schema & data
docker exec -i billtracker-db-1 mysql -uroot -p<rootpw> < docs/sql/schema.sql

# 4. Serve PHP (built-in server)
php -S localhost:8000

# 5. Open http://localhost:8000 in your browser
```

> **Tip:** The included `includes/db.php` auto-creates the database/tables on first run when connecting as a privileged MySQL user. For production, replace hard-coded credentials with environment variables.

---

## Project Structure

```
 billtracker/
 ├─ api/                # AJAX endpoints
 ├─ css/                # Stylesheets
 ├─ images/             # Assets
 ├─ includes/           # Reusable PHP partials & DB bootstrap
 ├─ js/                 # Front-end scripts
 ├─ pages/              # Login & other page fragments
 ├─ docs/
 │   ├─ screenshots/    # .png/.gif for README
 │   └─ sql/            # schema.sql & seed-data.sql
 ├─ .gitignore
 ├─ LICENSE
 └─ README.md
```

## License

Released under the MIT License – see `LICENSE` for details.

---

## Author

**Anoop Singh Panesar**

Feel free to reach out with questions about the project!
