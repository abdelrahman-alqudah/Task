# Task
A secure PHP user management and authentication system featuring JWt based authentication, PostgreSQL persistence, Redis session storage, and clean layered architecture (Models, DTO, Repository, Services) with input validation, password hashing, soft deletes, and UUID v7 identifiers.


## ⚙️ Environment Configuration

Create a `.env` file in the root directory of the project and configure the required environment variables based on the template below:

```env
# Application Settings
APP_ENV=development

# Database Configuration (MySQL / PostgreSQL / SQL Server)
DB_HOST=127.0.0.1
DB_PORT=5432
DB_USER=your_database_user
DB_PASSWORD=your_database_password
DB_DATABASE=your_database_name

# Redis Connection Settings
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_SCHEME=tcp

# JWT Authentication Settings
JWT_SECRET=your_super_secret_key_here_at_least_32_chars
JWT_ISSUER=kayan_users
JWT_AUDIENCE=kayan_users
JWT_EXPIRES_IN=3600
