# BillTracker

> A lightweight web application to keep track of recurring bills and their payment status. Built with **vanilla PHP 8**, **MySQL**, and a sprinkle of **HTML/CSS/JavaScript**. Perfect for demonstrating full‑stack fundamentals to prospective employers.

---

## ✨ Key Features

* **User authentication** – secure login/logout with role‑based access (admin & standard user)
* **CRUD bills** – add, edit, delete, and view upcoming bills
* **One‑click paid/unpaid toggle** – updates status instantly via AJAX
* **Automatic totals** – monthly & yearly spend at a glance
* **Bootstrap‑styled UI** – clean, responsive layout

---

## 📸 Demo

| Dashboard                                   | Toggle Paid                              |
| ------------------------------------------- | ---------------------------------------- |
| *Add `docs/screenshots/dashboard.png` here* | *Add `docs/screenshots/toggle.gif` here* |

---

## 🛠️ Tech Stack

| Layer         | What we used                                |
| ------------- | ------------------------------------------- |
| **Backend**   | PHP 8, MySQL 8                              |
| **Frontend**  | HTML5, CSS3 (Bootstrap 5), JavaScript (ES6) |
| **Dev Tools** | Git, VS Code, PHP CLI, MySQL Workbench      |

---

## 🚀 Quick Start (Local)

```bash
# 1. Clone the repo
$ git clone https://github.com/<your-user>/billtracker.git
$ cd billtracker

# 2. Spin up MySQL (Docker) – optional but easiest
$ docker compose up -d db

# 3. Import sample schema & data
$ docker exec -i billtracker-db-1 mysql -uroot -p<rootpw> < docs/sql/schema.sql

# 4. Serve PHP (built‑in server)
$ php -S localhost:8000

# 5. Open http://localhost:8000 in your browser
```

> **Tip:** The included `includes/db.php` auto‑creates the database/tables on first run when connecting as a privileged MySQL user. For production, replace hard‑coded creds with environment variables.

---

## 🗂️ Project Structure

```
 billtracker/
 ├─ api/                # AJAX endpoints
 ├─ css/                # Stylesheets
 ├─ images/             # Assets
 ├─ includes/           # Reusable PHP partials & DB bootstrap
 ├─ js/                 # Front‑end scripts
 ├─ pages/              # Login & other page fragments
 ├─ docs/
 │   ├─ screenshots/    # .png/.gif for README
 │   └─ sql/            # schema.sql & seed‑data.sql
 ├─ .gitignore
 ├─ LICENSE
 └─ README.md
```

---

## ⚙️ Configuration

| Setting       | Default     | Description    |
| ------------- | ----------- | -------------- |
| `DB_HOST`     | localhost   | MySQL host     |
| `DB_NAME`     | billtracker | Database name  |
| `DB_USER`     | root        | MySQL user     |
| `DB_PASSWORD` | (empty)     | MySQL password |

Update these in `includes/db.php` **or** create an `.env` file and read using `vlucas/phpdotenv` (todo).

---

## 📦 Deployment

* **Docker Compose** sample included – one service for PHP‑Apache, one for MySQL
* **GitHub Actions** CI workflow stub in `.github/workflows/php‑ci.yml` to run `php -l` (lint) and optionally PHPUnit tests

---

## 🧑‍💻 Contributing

1. Fork 💫
2. Create feature branch `git checkout -b feat/awesome`
3. Commit changes `git commit -m "feat: add awesome"`
4. Push `git push origin feat/awesome`
5. Open a PR

---

## 🪪 License

Released under the MIT License – see `LICENSE` for details.

---

## 🙋‍♂️ Author

| ![avatar](https://avatars.githubusercontent.com/anoopsingh)                                           |
| ----------------------------------------------------------------------------------------------------- |
| **Anoop Singh Panesar**<br>AWS Certified                                                              |

Feel free to reach out with questions about the project!
