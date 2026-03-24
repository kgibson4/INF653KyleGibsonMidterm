# Quotes REST API

A full-stack RESTful API for managing a database of quotes, authors, and categories. Built with PHP and PostgreSQL, containerized with Docker, and deployed on Render.

## 🛠️ Tech Stack
- **Backend:** PHP (Vanilla)
- **Database:** PostgreSQL (Hosted on Render)
- **Environment:** Docker & Docker Compose
- **Routing:** Apache `.htaccess`
- **Frontend:** HTML5/JavaScript (Dashboard for CRUD testing)

---

## 📂 Project Structure
- `/api` - Contains the API entry points for Authors, Categories, and Quotes.
- `/models` - PHP classes handling database logic (CRUD).
- `/config` - Database connection and environment configuration.
- `index.php` - The root UI dashboard for interacting with the API.

---

## 📡 API Endpoints

### 1. Authors
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| GET | `/api/authors/` | List all authors |
| GET | `/api/authors/?id=X` | Get a specific author |
| POST | `/api/authors/` | Create a new author |
| PUT | `/api/authors/` | Update an existing author |
| DELETE | `/api/authors/` | Delete an author |

### 2. Categories
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| GET | `/api/categories/` | List all categories |
| GET | `/api/categories/?id=X` | Get a specific category |
| POST | `/api/categories/` | Create a new category |
| PUT | `/api/categories/` | Update an existing category |
| DELETE | `/api/categories/` | Delete a category |

### 3. Quotes
| Method | Endpoint | Description |
| :--- | :--- | :--- |
| GET | `/api/quotes/` | List all quotes |
| GET | `/api/quotes/?id=X` | Get a specific quote |
| GET | `/api/quotes/?author_id=X` | Filter quotes by author |
| GET | `/api/quotes/?random=true` | Get a single random quote |
| POST | `/api/quotes/` | Create a new quote |
| PUT | `/api/quotes/` | Update an existing quote |
| DELETE | `/api/quotes/` | Delete a quote |

---

## 🧪 Requirements & Error Handling
- **Consistency:** All `POST`, `PUT`, and `DELETE` requests return a single JSON object.
- **Validation:** Missing parameters trigger a `Missing Required Parameters` message.
- **Integrity:** Creating or updating a quote requires valid `author_id` and `category_id` values; otherwise, a specific `Not Found` message is returned.
- **PostgreSQL:** Uses `RETURNING id` for reliable ID retrieval in cloud environments.

---

## ⚙️ Local Setup (Docker)
1. Clone the repository.
2. Ensure you have a `.env` file with your Render Postgres credentials.
3. Run the containers:
   ```bash
   docker-compose up -d